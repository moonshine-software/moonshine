<?php

declare(strict_types=1);

namespace MoonShine\Menu;

use Closure;
use MoonShine\Contracts\Menu\MenuElement;
use MoonShine\Resources\CustomPage;
use MoonShine\Resources\Resource;
use MoonShine\Traits\Makeable;

/**
 * @method static make(string $label, Resource|CustomPage|Closure|string $resource, string $icon = null)
 */
class MenuItem extends MenuSection implements MenuElement
{
    use Makeable;

    final public function __construct(
        string $label,
        Resource|CustomPage|Closure|string $resource,
        string $icon = null
    ) {
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

        if (is_null($this->resource) && (is_string($resource) || is_callable(
            $resource
        ))) {
            $this->link = $resource;
        }

        if ($icon) {
            $this->icon($icon);
        }
    }
}
