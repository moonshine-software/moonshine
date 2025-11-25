<?php

declare(strict_types=1);

namespace MoonShine\MenuManager;

use Closure;
use Illuminate\Support\Collection;
use Leeto\FastAttributes\Attributes;
use MoonShine\Contracts\Core\CrudPageContract;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\MenuManager\MenuAutoloaderContract;
use MoonShine\Contracts\MenuManager\MenuElementContract;
use MoonShine\Contracts\MenuManager\MenuFillerContract;
use MoonShine\MenuManager\Attributes\CanSee;
use MoonShine\MenuManager\Attributes\Group;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\MenuManager\Attributes\SkipMenu;
use ReflectionException;

/**
 * @phpstan-type PSMenuItem array{
 *     filler: class-string<MenuFillerContract>,
 *     canSee: string,
 *     position: null|int,
 * }
 *
 * @phpstan-type PSMenuGroup array{
 *     label: string,
 *     class: class-string<MenuFillerContract>,
 *     icon: string|null,
 *     canSee: string,
 *     translatable: bool,
 * }
 *
 * @phpstan-type PSMenuGroupWithItems array{group: PSMenuGroup, items: list<PSMenuItem>, position: null|int}
 * @phpstan-type PSMenu list<PSMenuItem|PSMenuGroupWithItems>
 */
final readonly class MenuAutoloader implements MenuAutoloaderContract
{
    public function __construct(private CoreContract $core)
    {
    }

    /**
     * @return PSMenu
     *
     * @throws ReflectionException
     */
    public function toArray(): array
    {
        /** @var array<string, PSMenuGroupWithItems> $items */
        $items = [];

        /**
         * @param  array<string, PSMenuGroupWithItems>  $items
         */
        $resolveItems = static function (
            MenuFillerContract $item,
            array &$items,
        ): void {
            /** @var array<string, PSMenuGroupWithItems> $items */

            $skip = Attributes::for($item, SkipMenu::class);

            if (! \is_null($skip->first()) || $item->skipMenu()) {
                return;
            }

            /** @var null|Group $group */
            $group = Attributes::for($item, Group::class)->first();
            /** @var null|CanSee $canSee */
            $canSee = Attributes::for($item, CanSee::class)->first();
            /** @var null|Order $order */
            $order = Attributes::for($item, Order::class)->first();

            $label = $group->label ?? $item->getGroup();
            $icon = $group->icon ?? $item->getGroupIcon();
            $position = $order->value ?? $item->getPosition();

            $namespace = $item::class;

            $data = ['filler' => $namespace, 'canSee' => $canSee->method ?? 'canSee', 'position' => $position];

            if ($label !== null) {
                $existingGroup = $items[$label] ?? null;

                $existingItems = new Collection($existingGroup['items'] ?? []);

                if (! $existingItems->pluck('filler')->contains($data['filler'])) {
                    $existingItems->push($data);
                }

                $items[$label] = [
                    'position' => $position,
                    'group' => [
                        'class' => $namespace,
                        'label' => $label,
                        'icon' => $icon,
                        'canSee' => $canSee->method ?? 'canSee',
                        'translatable' => $group?->translatable,
                    ],
                    'items' => $existingItems->all(),
                ];

                return;
            }

            $items[$namespace] = $data;
        };

        foreach ($this->core->getResources()->toArray() as $item) {
            /** @var MenuFillerContract $item */
            /** @var array<string, PSMenuGroupWithItems> $items */
            $resolveItems($item, $items);
        }

        $excludePages = static fn (PageContract $page): bool => ! $page instanceof CrudPageContract;

        /** @var Collection<int, PageContract> $pages */
        $pages = $this->core->getPages();

        foreach ($pages->filter($excludePages)->toArray() as $item) {
            /** @var MenuFillerContract $item */
            /** @var array<string, PSMenuGroupWithItems> $items */
            $resolveItems($item, $items);
        }

        /**
         * @var array<string, PSMenuItem|PSMenuGroupWithItems> $items
         */
        $sort = static function (array $items): Collection {
            /**
             * @var Collection<array-key, PSMenuItem|PSMenuGroupWithItems> $collection
             * @var array<string, PSMenuItem|PSMenuGroupWithItems> $items
             */
            $collection = (new Collection($items))->values();

            /** @phpstan-ignore return.type */
            return $collection
                ->sortBy(fn (array $item): mixed => $item['position'] ?? INF)
                ->values();
        };

        $result = $sort($items)->map(function (array $item) use ($sort): array {
            /** @phpstan-ignore isset.offset */
            if (isset($item['group'])) {
                /** @var list<PSMenuItem> $innerItems */
                $innerItems = $item['items'];

                $item['items'] = $sort($innerItems)->all();
            }

            return $item;
        });

        /** @var PSMenu */
        return $result->all();
    }

    /**
     * @param  PSMenu|null  $cached
     *
     * @return list<MenuElementContract>
     */
    public function resolve(?array $cached = null, bool $onlyIcons = false): array
    {
        return $this->generateMenu($cached ?? $this->toArray(), $onlyIcons);
    }

    /**
     * @param  PSMenu|list<PSMenuItem>  $data
     *
     * @return list<MenuElementContract>
     */
    private function generateMenu(array $data, bool $onlyIcons = false): array
    {
        $menu = [];

        foreach ($data as $item) {
            if (isset($item['group'])) {
                $group = $item['group'];
                $label = $this->core->getTranslator()->get($group['label']);
                $label = \is_string($label) ? $label : $group['label'];

                $menu[] = MenuGroup::make(
                    $group['translatable'] ? $label : $group['label'],
                    $this->generateMenu($item['items'], $onlyIcons),
                    $group['icon'],
                )->onlyIcon($onlyIcons)->when($group['canSee'], fn (MenuGroup $ctx): MenuGroup => $ctx->canSee($this->canSee($group['class'], $group['canSee'])));

                continue;
            }

            if (isset($item['filler'])) {
                $menu[] = $this->toMenuItem($item['filler'], $item['canSee'] ?? 'canSee')->onlyIcon($onlyIcons);
            }
        }

        return $menu;
    }

    /**
     * @param  class-string<MenuFillerContract>  $filler
     */
    private function toMenuItem(string $filler, string $canSee): MenuItem
    {
        /** @var MenuFillerContract $resolved */
        $resolved = $this->core->getContainer($filler);

        return MenuItem::make($filler)
            ->when($canSee, fn (MenuItem $item): MenuItem => $item->canSee($this->canSee($resolved, $canSee)));
    }

    /**
     * @param  MenuFillerContract|class-string<MenuFillerContract>  $filler
     * @return Closure(): bool
     */
    private function canSee(string|MenuFillerContract $filler, string $method): Closure
    {
        $resolved = \is_string($filler) ? $this->core->getContainer($filler) : $filler;

        return static fn (): bool => (bool) $resolved->{$method}();
    }
}
