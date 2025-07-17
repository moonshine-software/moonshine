<?php

declare(strict_types=1);

namespace MoonShine\Core;

use MoonShine\Contracts\Core\CrudResourceContract;
use MoonShine\Contracts\Core\DependencyInjection\EndpointsContract;
use MoonShine\Contracts\Core\DependencyInjection\RouterContract;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\ResourceContract;
use Throwable;

/**
 * @template TResource of ResourceContract = ResourceContract
 *
 * @implements EndpointsContract<TResource>
 */
abstract readonly class AbstractEndpoints implements EndpointsContract
{
    public function __construct(
        protected RouterContract $router
    ) {
    }

    /**
     * @param  class-string<PageContract>|PageContract|null  $page
     * @param  class-string<TResource>|TResource|null  $resource
     * @param  array<string, string|int|float|null>  $params
     * @param  array<string, mixed>  $extra
     *
     * @throws Throwable
     */
    abstract public function toPage(
        string|PageContract|null $page = null,
        string|ResourceContract|null $resource = null,
        array $params = [],
        array $extra = [],
    ): mixed;

    abstract public function home(): string;

    /**
     * @param  array<string, string|int|float|null>  $params
     * @param null|TResource $resource
     */
    public function method(
        string $method,
        ?string $message = null,
        array $params = [],
        ?PageContract $page = null,
        ?ResourceContract $resource = null
    ): string {
        return $this->router->to('method', [
            'method' => $method,
            'message' => $message,
            ...$params,
            ...[
                'pageUri' => $this->router->getParam('pageUri', $this->router->extractPageUri($page)),
                'resourceUri' => $this->router->getParam('resourceUri', $this->router->extractResourceUri($resource)),
            ],
        ]);
    }

    /**
     * @param  TResource|null  $resource
     * @param  array<string, mixed>  $extra
     */
    public function reactive(
        ?PageContract $page = null,
        ?ResourceContract $resource = null,
        array $extra = []
    ): string {
        /** @var int|string|null $key */
        $key = $extra['key'] ?? null;

        if ($key === null && $resource instanceof CrudResourceContract) {
            $key = $resource->getCastedData()?->getKey();
        }

        return $this->router->to('reactive', [
            'resourceItem' => $key,
            'pageUri' => $this->router->getParam('pageUri', $this->router->extractPageUri($page)),
            'resourceUri' => $this->router->getParam('resourceUri', $this->router->extractResourceUri($resource)),
        ]);
    }

    /**
     * @param  array<string, mixed>  $additionally
     */
    public function component(
        string $name,
        array $additionally = []
    ): string {
        /** @var int|string|null $key */
        $key = $additionally['resourceItem'] ?? null;

        return $this->router->to('component', [
            '_component_name' => $name,
            '_parentId' => $this->router->getParam('_parentId'),
            ...$additionally,
            ...array_filter([
                'pageUri' => $this->router->extractPageUri(),
                'resourceUri' => $this->router->extractResourceUri(),
                'resourceItem' => $this->router->extractResourceItem($key ?? null),
            ]),
        ]);
    }

    /**
     * @param null|TResource $resource
     * @param array<string, mixed> $extra
     */
    public function updateField(
        ?ResourceContract $resource = null,
        ?PageContract $page = null,
        array $extra = [],
    ): string {
        /** @var string|null $relation */
        $relation = $extra['relation'] ?? null;
        /** @var string|int|null $resourceItem */
        $resourceItem = $extra['resourceItem'] ?? null;
        $through = $relation ? 'relation' : 'column';

        return $this->withRelation(
            "update-field.through-$through",
            resourceItem: $this->router->extractResourceItem($resourceItem),
            relation: $relation,
            resourceUri: $resource ? $resource->getUriKey() : $this->router->extractResourceUri(),
            pageUri: $page ? $page->getUriKey() : $this->router->extractPageUri()
        );
    }

    public function withRelation(
        string $action,
        int|string|null $resourceItem = null,
        ?string $relation = null,
        ?string $resourceUri = null,
        ?string $pageUri = null,
        ?string $parentField = null,
    ): string {
        return $this->router->to($action, [
            'pageUri' => $pageUri ?? $this->router->extractPageUri(),
            'resourceUri' => $resourceUri ?? $this->router->extractResourceUri(),
            'resourceItem' => $resourceItem,
            '_parent_field' => $parentField,
            '_relation' => $relation,
            '_no_items_query' => true,
        ]);
    }
}
