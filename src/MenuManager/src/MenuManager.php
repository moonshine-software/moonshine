<?php

declare(strict_types=1);

namespace MoonShine\MenuManager;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Conditionable;
use MoonShine\Contracts\MenuManager\MenuElementContract;
use MoonShine\Contracts\MenuManager\MenuElementsContract;
use MoonShine\Contracts\MenuManager\MenuManagerContract;

final class MenuManager implements MenuManagerContract
{
    use Conditionable;

    /**
     * @var array<MenuElementContract>|list<MenuElementContract>
     */
    private array $items = [];

    /**
     * @var list<MenuCondition>
     */
    private array $conditionItems = [];

    private bool $topMode = false;

    public function add(array|MenuElementContract $data): static
    {
        $this->items = array_merge(
            $this->items,
            \is_array($data) ? $data : [$data]
        );

        return $this;
    }

    public function remove(Closure $condition): static
    {
        /** @var list<MenuElementContract> $items */
        $items = (new Collection($this->items))
            ->reject($condition)
            ->toArray();

        $this->items = $items;

        return $this;
    }

    public function addBefore(Closure $before, array|MenuElementContract|Closure $data): static
    {
        $this->conditionItems[] = new MenuCondition($data, before: $before);

        return $this;
    }

    public function addAfter(Closure $after, array|MenuElementContract|Closure $data): static
    {
        $this->conditionItems[] = new MenuCondition($data, after: $after);

        return $this;
    }

    /**
     * @param  ?Closure(static): bool  $condition
     */
    public function topMode(?Closure $condition = null): static
    {
        $this->topMode = \is_null($condition) || $condition($this) === true;

        return $this;
    }

    public function all(?iterable $items = null): MenuElementsContract
    {
        return MenuElements::make($items ?? $this->items)
            ->map(static function (array|MenuElementContract $item): MenuElementContract {
                /** @var array{url: string, label: string}|MenuElementContract $item */
                /** @phpstan-ignore-next-line  */
                if (\is_array($item)) {
                    return MenuItem::make($item['url'], $item['label']);
                }

                return $item;
            })
            ->onlyVisible()
            ->when(
                $this->conditionItems !== [],
                function (MenuElementsContract $elements): MenuElementsContract {
                    foreach ($this->conditionItems as $conditionItem) {
                        $elements->each(static function (MenuElementContract $element, int $index) use ($elements, $conditionItem): void {
                            $elements->when(
                                $conditionItem->hasBefore() && $conditionItem->isBefore($element),
                                static fn (MenuElementsContract $e) => $e->splice($index, 0, (array) $conditionItem->getData())
                            )->when(
                                $conditionItem->hasAfter() && $conditionItem->isAfter($element),
                                static fn (MenuElementsContract $e) => $e->splice($index + 1, 0, (array) $conditionItem->getData())
                            );
                        });
                    }

                    return $elements;
                }
            )->when(
                $this->topMode,
                static fn (MenuElementsContract $elements): MenuElementsContract => $elements->topMode()
            );
    }

    public function flushState(): void
    {
        $this->items = [];
        $this->conditionItems = [];
    }
}
