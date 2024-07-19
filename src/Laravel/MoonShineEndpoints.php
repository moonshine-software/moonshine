<?php

declare(strict_types=1);

namespace MoonShine\Laravel;

use MoonShine\Contracts\Core\DependencyInjection\EndpointsContract;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Core\Exceptions\MoonShineException;
use MoonShine\Core\Pages\Pages;
use MoonShine\Laravel\DependencyInjection\MoonShineRouter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Throwable;

final readonly class MoonShineEndpoints implements EndpointsContract
{
    public function __construct(
        private MoonShineRouter $router
    ) {
    }

    public function asyncMethod(
        string $method,
        ?string $message = null,
        array $params = [],
        ?PageContract $page = null,
        ?ResourceContract $resource = null
    ): string {
        return $this->router->to('async.method', [
            'method' => $method,
            'message' => $message,
            ...$params,
            ...[
                'pageUri' => $this->router->getParam('pageUri', $this->router->extractPageUri($page)),
                'resourceUri' => $this->router->getParam('resourceUri', $this->router->extractResourceUri($resource)),
            ],
        ]);
    }

    public function reactive(
        ?PageContract $page = null,
        ?ResourceContract $resource = null,
        array $extra = []
    ): string {
        $key = $extra['key'] ?? $resource?->getItem()?->getKey();

        return $this->router->to('async.reactive', [
            'resourceItem' => $key,
            'pageUri' => $this->router->getParam('pageUri', $this->router->extractPageUri($page)),
            'resourceUri' => $this->router->getParam('resourceUri', $this->router->extractResourceUri($resource)),
        ]);
    }

    public function asyncComponent(
        string $name,
        array $additionally = []
    ): string {
        return $this->router->to('async.component', [
                '_component_name' => $name,
                '_parentId' => moonshineRequest()->getParentResourceId(),
                ...$additionally,
                ...[
                    'pageUri' => $this->router->extractPageUri(),
                    'resourceUri' => $this->router->extractResourceUri(),
                ],
            ]);
    }

    public function updateColumn(
        ?ResourceContract $resource = null,
        ?PageContract $page = null,
        array $extra = [],
    ): string {
        $relation = $extra['relation'] ?? null;
        $resourceItem = $extra['resourceItem'] ?? null;

        return $this->router->to(
            'column.' . ($relation ? 'relation' : 'resource') . '.update-column',
            [
                'resourceUri' => $resource ? $resource->getUriKey() : $this->router->extractResourceUri(),
                'pageUri' => $page ? $page->getUriKey() : $this->router->extractPageUri(),
                'resourceItem' => $this->router->extractResourceItem($resourceItem),
                '_relation' => $relation,
            ]
        );
    }

    public function toRelation(
        string $action,
        int|string|null $resourceItem = null,
        ?string $relation = null,
        ?string $resourceUri = null,
        ?string $pageUri = null,
        ?string $parentField = null,
    ): string {
        return $this->router->to("relation.$action", [
            'pageUri' => $pageUri ?? moonshineRequest()->getPageUri(),
            'resourceUri' => $resourceUri ?? moonshineRequest()->getResourceUri(),
            'resourceItem' => $resourceItem,
            '_parent_field' => $parentField,
            '_relation' => $relation,
        ]);
    }

    /**
     * @throws Throwable
     */
    public function toPage(
        string|PageContract|null $page = null,
        string|ResourceContract|null $resource = null,
        array $params = [],
        array $extra = [],
    ): string|RedirectResponse {
        $targetPage = null;

        $redirect = $extra['redirect'] ?? false;
        $fragment = $extra['fragment'] ?? null;

        if ($fragment !== null && $fragment !== '') {
            $params += ['_fragment-load' => $fragment];
        }

        throw_if(
            is_null($page) && is_null($resource),
            new MoonShineException('Page or resource must not be null')
        );

        if (! is_null($resource)) {
            $targetResource = $resource instanceof ResourceContract
                ? $resource
                : moonshine()->getResources()->findByClass($resource);

            $targetPage = $targetResource?->getPages()->when(
                is_null($page),
                static fn (Pages $pages) => $pages->first(),
                static fn (Pages $pages): ?PageContract => $pages->findByUri(
                    $page instanceof PageContract
                        ? $page->getUriKey()
                        : MoonShineRouter::uriKey($page)
                ),
            );
        }

        if (is_null($resource)) {
            $targetPage = $page instanceof PageContract
                ? $page
                : moonshine()->getPages()->findByClass($page);
        }

        throw_if(
            is_null($targetPage),
            new MoonShineException('Page not exists')
        );

        return $redirect
            ? redirect($targetPage->getRoute($params))
            : $targetPage->getRoute($params);
    }

    public function home(): string
    {
        return route(
            moonshineConfig()->getHomeRoute()
        );
    }
}
