<?php

declare(strict_types=1);

namespace MoonShine\MenuManager;

use Closure;
use Illuminate\Support\Traits\Conditionable;

final class MenuManager
{
    use Conditionable;

    /**
     * @var list<MenuElement>
     */
    private array $items = [];

    /**
     * @var list<MenuCondition>
     */
    private array $conditionItems = [];

    private bool $topMode = false;

    public function add(array|MenuElement $data): self
    {
        $this->items = array_merge(
            $this->items,
            is_array($data) ? $data : [$data]
        );

        return $this;
    }

    public function remove(Closure $condition): self
    {
        $this->items = collect($this->items)
            ->reject($condition)
            ->toArray();

        return $this;
    }

    public function addBefore(Closure $before, array|MenuElement|Closure $data): self
    {
        $this->conditionItems[] = new MenuCondition($data, before: $before);

        return $this;
    }

    public function addAfter(Closure $after, array|MenuElement|Closure $data): self
    {
        $this->conditionItems[] = new MenuCondition($data, after: $after);

        return $this;
    }

    public function topMode(?Closure $condition = null): self
    {
        $this->topMode = is_null($condition) || value($condition, $this);

        return $this;
    }

    public function all(?iterable $items = null): MenuElements
    {
        return MenuElements::make($items ?: $this->items)
            ->onlyVisible()
            ->when(
                $this->conditionItems !== [],
                function (MenuElements $elements): MenuElements {
                    foreach ($this->conditionItems as $conditionItem) {
                        $elements->each(function (MenuElement $element, int $index) use ($elements, $conditionItem): void {
                            $elements->when(
                                $conditionItem->hasBefore() && $conditionItem->isBefore($element),
                                fn (MenuElements $e) => $e->splice($index, 0, $conditionItem->getData())
                            )->when(
                                $conditionItem->hasAfter() && $conditionItem->isAfter($element),
                                fn (MenuElements $e) => $e->splice($index + 1, 0, $conditionItem->getData())
                            );
                        });
                    }

                    return $elements;
                }
            )->when(
                $this->topMode,
                fn (MenuElements $elements): MenuElements => $elements->topMode()
            );
    }
}
