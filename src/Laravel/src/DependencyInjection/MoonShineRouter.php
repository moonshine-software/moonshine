<?php

declare(strict_types=1);

namespace MoonShine\Laravel\DependencyInjection;

use MoonShine\Contracts\Core\DependencyInjection\EndpointsContract;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Core\AbstractRouter;
use MoonShine\Laravel\MoonShineEndpoints;

final class MoonShineRouter extends AbstractRouter
{
    public function getEndpoints(): EndpointsContract
    {
        return new MoonShineEndpoints($this);
    }

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

    public function extractPageUri(?PageContract $page = null): ?string
    {
        return $page
            ? $page->getUriKey()
            : $this->getParam('pageUri', moonshineRequest()->getPageUri());
    }

    public function extractResourceUri(?ResourceContract $resource = null): ?string
    {
        return $resource
            ? $resource->getUriKey()
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
