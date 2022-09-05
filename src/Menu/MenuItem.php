<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Menu;

use JsonSerializable;
use Leeto\MoonShine\Contracts\ResourceContract;
use Leeto\MoonShine\Traits\Makeable;

final class MenuItem extends MenuSection implements JsonSerializable
{
    use Makeable;

    final public function __construct(string $title, ResourceContract|string $resource, string $icon = null)
    {
        $this->title = $title;
        $this->resource = is_string($resource) ? new $resource() : $resource;

        if ($icon) {
            $this->icon($icon);
        }
    }

    public function jsonSerialize(): array
    {
        return [
            'title' => $this->title(),
            'icon' => $this->getIcon(),
            'resource' => $this->resource()->uriKey(),
        ];
    }
}
