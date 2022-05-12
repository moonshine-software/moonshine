<?php

namespace Leeto\MoonShine\Menu;

class BaseMenuSection
{
    protected string $title;

    protected string|null $icon = null;

    public static function make(...$arguments): static
    {
        return new static(...$arguments);
    }

    public function title(): String
    {
        return $this->title;
    }

    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
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