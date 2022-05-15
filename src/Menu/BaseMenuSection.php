<?php

namespace Leeto\MoonShine\Menu;

use Illuminate\Support\Collection;
use Leeto\MoonShine\Resources\BaseResource;

abstract class BaseMenuSection
{
    protected string $title;

    protected string|null $icon = null;

    protected Collection $items;

    protected BaseResource $resource;

    public function title(): String
    {
        return $this->title;
    }

    public function items(): Collection
    {
        return $this->items;
    }

    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function resource(): BaseResource
    {
        return $this->resource;
    }

    public function getIcon(string $size = '8', string $color = '', string $class = ''): string
    {
        $icon = $this->icon ?? 'app';

        return view("moonshine::shared.icons.$icon", compact('size', 'color', 'class'));
    }

    public function isGroup(): bool
    {
        return $this instanceof MenuGroup;
    }

    public function isActive(): bool
    {
        if($this->isGroup()) {
            foreach ($this->items() as $item) {
                if($item->isActive()) {
                    return true;
                }
            }

            return false;
        } else {
            return request()->routeIs($this->resource()->routeName('*'));
        }
    }
}