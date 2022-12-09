<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Menu;

use Leeto\MoonShine\Resources\Resource;
use Leeto\MoonShine\Traits\Makeable;

class MenuItem extends MenuSection
{
    use Makeable;

    final public function __construct(string $title, Resource|string $resource, string $icon = null)
    {
        $this->title = $title;

        if($resource instanceof Resource) {
            $this->resource = $resource;
        }

        if(is_string($resource) && class_exists($resource)) {
            $this->resource = new $resource;
        }

        if(is_string($resource) && is_null($this->resource)) {
            $this->link = $resource;
        }

        if ($icon) {
            $this->icon($icon);
        }
    }
}
