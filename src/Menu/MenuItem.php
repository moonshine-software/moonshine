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
        $this->resource = is_string($resource) ? new $resource() : $resource;

        if ($icon) {
            $this->icon($icon);
        }
    }
}
