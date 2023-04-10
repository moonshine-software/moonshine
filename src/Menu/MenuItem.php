<?php

declare(strict_types=1);

namespace MoonShine\Menu;

use MoonShine\Resources\CustomPage;
use MoonShine\Resources\Resource;
use MoonShine\Traits\Makeable;

class MenuItem extends MenuSection
{
    use Makeable;

    final public function __construct(string $label, Resource|CustomPage|string $resource, string $icon = null)
    {
        $this->setLabel($label);

        if ($resource instanceof Resource) {
            $this->resource = $resource;
        }

        if ($resource instanceof CustomPage) {
            $this->page = $resource;
        }

        if (is_string($resource) && class_exists($resource)) {
            $this->resource = new $resource();
        }

        if (is_string($resource) && is_null($this->resource)) {
            $this->link = $resource;
        }

        if ($icon) {
            $this->icon($icon);
        }
    }
}
