<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Menu;

use Illuminate\Support\Collection;
use Leeto\MoonShine\Contracts\ResourceContract;
use Leeto\MoonShine\Traits\WithIcon;

abstract class MenuSection
{
    use WithIcon;

    protected string $title;

    protected Collection $items;

    protected ResourceContract $resource;

    public function title(): string
    {
        return $this->title;
    }

    public function items(): Collection
    {
        return $this->items;
    }

    public function resource(): ResourceContract
    {
        return $this->resource;
    }

    public function isGroup(): bool
    {
        return $this instanceof MenuGroup;
    }

    public function isActive(): bool
    {
        if ($this->isGroup()) {
            foreach ($this->items() as $item) {
                if ($item->isActive()) {
                    return true;
                }
            }

            return false;
        } else {
            return request()->routeIs($this->resource()->routeName('*'));
        }
    }
}
