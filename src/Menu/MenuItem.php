<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Menu;

use JsonSerializable;
use Leeto\MoonShine\Contracts\Resources\ResourceContract;
use Leeto\MoonShine\Traits\Makeable;

final class MenuItem extends MenuSection implements JsonSerializable
{
    use Makeable;

    final public function __construct(
        ResourceContract|string $data,
        string $title = null,
        protected ?string $routeName = null
    ) {
        if (is_string($data)) {
            $this->url = $data;
        } else {
            $this->resource = $data;
            $this->title = $title ?? $this->resource->title();
        }
    }

    public function jsonSerialize(): array
    {
        return [
            'title' => $this->title(),
            'icon' => $this->getIcon(),
            $this->resource()
                ? 'resource'
                : 'url' => $this->resource()
                ? $this->resource()->uriKey()
                : $this->url(),
        ];
    }
}
