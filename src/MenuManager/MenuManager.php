<?php

declare(strict_types=1);

namespace MoonShine\MenuManager;

use Closure;
use Illuminate\Support\Traits\Conditionable;
use MoonShine\Contracts\Core\DependencyInjection\RequestContract;
use MoonShine\Contracts\Core\DependencyInjection\RouterContract;
use MoonShine\Contracts\MenuManager\MenuElementsContract;
use MoonShine\Contracts\MenuManager\MenuElementContract;
use MoonShine\Contracts\MenuManager\MenuManagerContract;

final class MenuManager implements MenuManagerContract
{
    use Conditionable;

    /**
     * @var list<MenuElementContract>
     */
    private array $items = [];

    /**
     * @var list<MenuCondition>
     */
    private array $conditionItems = [];

    private bool $topMode = false;

    public function __construct(
        private RequestContract $request,
        private RouterContract $router,
    )
    {
    }

    public function add(array|MenuElementContract $data): static
    {
        $this->items = array_merge(
            $this->items,
            is_array($data) ? $data : [$data]
        );

        return $this;
    }

    public function remove(Closure $condition): static
    {
        $this->items = collect($this->items)
            ->reject($condition)
            ->toArray();

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

    public function topMode(?Closure $condition = null): static
    {
        $this->topMode = is_null($condition) || value($condition, $this);

        return $this;
    }

    public function all(?iterable $items = null): MenuElementsContract
    {
        return MenuElements::make($items ?: $this->items)
            ->onlyVisible()
            ->when(
                $this->conditionItems !== [],
                function (MenuElementsContract $elements): MenuElementsContract {
                    foreach ($this->conditionItems as $conditionItem) {
                        $elements->each(static function (MenuElementContract $element, int $index) use ($elements, $conditionItem): void {
                            $elements->when(
                                $conditionItem->hasBefore() && $conditionItem->isBefore($element),
                                static fn (MenuElementsContract $e) => $e->splice($index, 0, $conditionItem->getData())
                            )->when(
                                $conditionItem->hasAfter() && $conditionItem->isAfter($element),
                                static fn (MenuElementsContract $e) => $e->splice($index + 1, 0, $conditionItem->getData())
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
}
