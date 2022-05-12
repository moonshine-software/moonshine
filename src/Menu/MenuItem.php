<?php

namespace Leeto\MoonShine\Menu;

use Leeto\MoonShine\Resources\BaseResource;

class MenuItem extends BaseMenuSection
{
    protected BaseResource $resource;

    public function __construct(string $title, BaseResource|string $resource, string $icon = null)
    {
        $this->title = $title;
        $this->resource = is_string($resource) ? new $resource() : $resource;

        if($icon) {
            $this->icon($icon);
        }
    }

    public function resource(): BaseResource
    {
       return $this->resource;
    }
}