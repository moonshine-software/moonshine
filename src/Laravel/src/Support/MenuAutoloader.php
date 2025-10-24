<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Support;

use Closure;
use Illuminate\Support\Collection;
use Leeto\FastAttributes\Attributes;
use MoonShine\Contracts\Core\CrudPageContract;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\MenuManager\MenuAutoloaderContract;
use MoonShine\Contracts\MenuManager\MenuElementContract;
use MoonShine\Contracts\MenuManager\MenuFillerContract;
use MoonShine\Laravel\DependencyInjection\MoonShine;
use MoonShine\MenuManager\Attributes\CanSee;
use MoonShine\MenuManager\Attributes\Group;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\MenuManager\Attributes\SkipMenu;
use MoonShine\MenuManager\MenuGroup;
use MoonShine\MenuManager\MenuItem;

/**
 * @internal
 * @phpstan-type PSMenuItem array{
 *      filler: class-string<MenuFillerContract>,
 *      canSee: null|string,
 *      position: null|int,
 *  }
 *
 * @phpstan-type PSMenuGroup array{
 *       label: string,
 *       class: class-string<MenuFillerContract>,
 *       icon: string|null,
 *       canSee: null|string,
 *       translatable: bool,
 *   }
 *
 * @phpstan-type PSMenu array{
 *      string,
 *      PSMenuItem|array{
 *           group: PSMenuGroup,
 *           items: list<PSMenuItem>,
 *           position: null|int,
 *      }
 *  }
 */
final readonly class MenuAutoloader implements MenuAutoloaderContract
{
    /**
     * @param  MoonShine  $core
     */
    public function __construct(private CoreContract $core)
    {
    }

    /**
     * @return PSMenu
     */
    public function toArray(): array
    {
        $items = [];

        $resolveItems = static function (
            MenuFillerContract $item,
            &$items,
        ): void {
            $skip = Attributes::for($item, SkipMenu::class);

            if (! \is_null($skip->first())) {
                return;
            }

            $group = Attributes::for($item, Group::class)->first();
            $canSee = Attributes::for($item, CanSee::class)->first();
            $order = Attributes::for($item, Order::class)->first();

            $label = $group?->label;
            $icon = $group?->icon;
            $position = $order?->value;

            $namespace = $item::class;
            $data = ['filler' => $namespace, 'canSee' => $canSee?->method, 'position' => $position];

            if ($label !== null) {
                $existingGroup = $items[$label] ?? null;

                $existingItems = collect($existingGroup['items'] ?? []);

                if (! $existingItems->pluck('filler')->contains($data['filler'])) {
                    $existingItems->push($data);
                }

                $items[$label] = [
                    'position' => $position,
                    'group' => ['class' => $namespace, 'label' => $label, 'icon' => $icon, 'canSee' => $canSee?->method, 'translatable' => $group?->translatable],
                    'items' => $existingItems->all(),
                ];

                return;
            }

            $items[$namespace] = $data;
        };

        foreach ($this->core->getResources()->toArray() as $item) {
            $resolveItems($item, $items);
        }

        $excludePages = static fn (PageContract $page): bool => ! $page instanceof CrudPageContract;

        foreach ($this->core->getPages()->filter($excludePages)->toArray() as $item) {
            $resolveItems($item, $items);
        }

        $sort = static fn ($items) => (new Collection($items))->values()
            ->sortBy(fn ($item): mixed => $item['position'] ?? INF)
            ->values();

        $result = $sort($items)->map(function ($item) use ($sort) {
            if (isset($item['group'])) {
                $item['items'] = $sort($item['items'])->all();
            }

            return $item;
        });

        return $result->all();
    }

    /**
     * @param  PSMenu|null  $cached
     *
     * @return MenuElementContract[]
     */
    public function resolve(?array $cached = null): array
    {
        return $this->generateMenu($cached ?? $this->toArray());
    }

    /**
     * @param  PSMenu|list<PSMenuItem>  $data
     *
     * @return list<MenuElementContract>
     */
    private function generateMenu(array $data): array
    {
        $menu = [];

        foreach ($data as $item) {
            if (isset($item['group'])) {
                $group = $item['group'];
                $menu[] = MenuGroup::make(
                    $group['translatable'] ? __($group['label']) : $group['label'],
                    $this->generateMenu($item['items']),
                    $group['icon'],
                )->when($group['canSee'], fn (MenuGroup $ctx): MenuGroup => $ctx->canSee($this->canSee($group['class'], $group['canSee'])));

                continue;
            }

            $menu[] = $this->toMenuItem($item['filler'], $item['canSee']);
        }

        return $menu;
    }

    /**
     * @param  class-string<MenuFillerContract>  $filler
     */
    private function toMenuItem(string $filler, ?string $canSee = null): MenuItem
    {
        $resolved = app($filler);
        $label = $resolved->getTitle();

        return MenuItem::make($label, $filler)
            ->when($canSee, fn (MenuItem $item): MenuItem => $item->canSee($this->canSee($resolved, $canSee)));
    }

    /**
     * @param  MenuFillerContract|class-string<MenuFillerContract>  $filler
     */
    private function canSee(string|MenuFillerContract $filler, string $method): Closure
    {
        if (\is_string($filler)) {
            $filler = app($filler);
        }

        return static fn () => $filler->{$method}();
    }
}
