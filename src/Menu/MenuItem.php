<?php

namespace Leeto\MoonShine\Menu;

use Leeto\MoonShine\Resources\BaseResource;

class MenuItem extends BaseMenuSection
{
    public static function make(...$arguments): static
    {
        return new static(...$arguments);
    }

    final public function __construct(string $title, BaseResource|string $resource, string $icon = null)
    {
        $this->title = $title;
        $this->resource = is_string($resource) ? new $resource() : $resource;

        if($icon) {
            $this->icon($icon);
        }
    }
}