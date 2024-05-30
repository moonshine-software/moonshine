<?php

declare(strict_types=1);

namespace MoonShine\Laravel;

use MoonShine\Core\Contracts\ResourceContract;
use MoonShine\Core\MoonShineRouter;
use MoonShine\Core\Pages\Page;

final class LaravelMoonShineRouter extends MoonShineRouter
{
    public function to(string $name = '', array $params = []): string
    {
        return route(
            $this->getName($name),
            $this->getParams($params)
        );
    }

    public function withResourceItem(int|string|null $key = null, ?ResourceContract $resource = null): self
    {
        if (! is_null($key = $this->extractResourceItem($key, $resource))) {
            return $this->withParams([
                'resourceItem' => $key,
            ]);
        }

        return $this;
    }

    public function extractPageUri(?Page $page = null): ?string
    {
        return $page
            ? $page->uriKey()
            : $this->getParam('pageUri', moonshineRequest()->getPageUri());
    }

    public function extractResourceUri(?ResourceContract $resource = null): ?string
    {
        return $resource
            ? $resource->uriKey()
            : $this->getParam('resourceUri', moonshineRequest()->getResourceUri());
    }

    public function extractResourceItem(
        int|string|null $key = null,
        ?ResourceContract $resource = null
    ): string|int|null {
        if (is_null($key)) {
            $key = $resource
                ? $resource->getItem()?->getKey()
                : $this->getParam('resourceItem', moonshineRequest()->getResource()?->getItemID());
        }

        return $key;
    }
}
