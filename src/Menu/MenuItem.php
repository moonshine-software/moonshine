<?php

namespace Leeto\MoonShine\Menu;

use Leeto\MoonShine\Resources\Resource;

class MenuItem extends MenuSection
{
    public static function make(...$arguments): static
    {
        return new static(...$arguments);
    }

    final public function __construct(string $title, Resource|string $resource, string $icon = null)
    {
        $this->title = $title;
        $this->resource = is_string($resource) ? new $resource() : $resource;

        if($icon) {
            $this->icon($icon);
        }
    }
}