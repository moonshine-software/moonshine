<?php

declare(strict_types=1);

namespace MoonShine\Laravel\DependencyInjection;

use MoonShine\Contracts\Core\CrudResourceContract;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Core\AbstractRouter;
use MoonShine\Laravel\MoonShineEndpoints;

class MoonShineRouter extends AbstractRouter
{
    public function getEndpoints(): MoonShineEndpoints
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

    public function extractPageUri(?PageContract $page = null): ?string
    {
        /** @var string|null */
        return $page instanceof PageContract
            ? $page->getUriKey()
            : $this->getParam('pageUri', moonshineRequest()->getPageUri());
    }

    public function extractResourceUri(?ResourceContract $resource = null): ?string
    {
        /** @var string|null */
        return $resource instanceof ResourceContract
            ? $resource->getUriKey()
            : $this->getParam('resourceUri', moonshineRequest()->getResourceUri());
    }

    public function extractResourceItem(
        int|string|null $key = null,
        ?CrudResourceContract $resource = null
    ): string|int|null {
        if (\is_null($key)) {
            if ($resource instanceof CrudResourceContract) {
                $item = $resource->getItem();

                return $item === null ? null : $resource->getCaster()->cast($item)->getKey();
            }

            /** @var int|string|null */
            return $this->getParam('resourceItem', moonshineRequest()->getResource()?->getItemID());
        }

        return $key;
    }
}
