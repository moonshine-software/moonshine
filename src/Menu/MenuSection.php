<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Menu;

use Illuminate\Support\Collection;
use Leeto\MoonShine\Resources\Resource;

abstract class MenuSection
{
    protected string $title;

    protected string|null $icon = null;

    protected Collection $items;

    protected Resource $resource;

    public function title(): string
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

    public function resource(): Resource
    {
        return $this->resource;
    }

    public function getIcon(
        string $size = '8',
        string $color = '',
        string $class = ''
    ): \Illuminate\Contracts\View\View
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
