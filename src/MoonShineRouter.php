<?php

declare(strict_types=1);

namespace MoonShine;

use Illuminate\Support\Stringable;
use Illuminate\Support\Traits\Conditionable;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Core\Exceptions\MoonShineException;
use MoonShine\Core\Pages\Page;
use MoonShine\Core\Pages\Pages;
use Stringable as NativeStringable;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Throwable;

// TODO @isolate (route,redirect,request)
final class MoonShineRouter implements NativeStringable
{
    use Conditionable;

    public const ROUTE_PREFIX = 'moonshine';

    public function __construct(
        private string $name = '',
        private array $params = [],
    ) {
    }

    public function withName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getRawName(): string
    {
        return $this->name;
    }

    public function getName(string $name = ''): string
    {
        return str($this->getRawName())
            ->prepend(self::ROUTE_PREFIX . '.')
            ->when(
                $name,
                fn (Stringable $str) => $str
                    ->trim('.')
                    ->append(".$name")
            )
            ->value();
    }

    public function withParams(array $params): self
    {
        $this->params = array_merge(
            $params,
            $this->params,
        );

        return $this;
    }

    public function withPage(?Page $page = null): self
    {
        if (! is_null($pageUri = $this->extractPageUri($page))) {
            return $this->withParams([
                'pageUri' => $pageUri,
            ]);
        }

        return $this;
    }

    private function extractPageUri(?Page $page = null): ?string
    {
        return $page
            ? $page->uriKey()
            : $this->getParam('pageUri', moonshineRequest()->getPageUri());
    }

    public function withResource(?ResourceContract $resource = null): self
    {
        if (! is_null($resourceUri = $this->extractResourceUri($resource))) {
            return $this->withParams([
                'resourceUri' => $resourceUri,
            ]);
        }

        return $this;
    }

    private function extractResourceUri(?ResourceContract $resource = null): ?string
    {
        return $resource
            ? $resource->uriKey()
            : $this->getParam('resourceUri', moonshineRequest()->getResourceUri());
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

    private function extractResourceItem(
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

    public function getParams(array $params = []): array
    {
        return array_filter(
            array_merge(
                $this->params,
                $params
            ),
            static fn ($value) => filled($value)
        );
    }

    public function getParam(string $key, mixed $default = null): mixed
    {
        return data_get($this->getParams(), $key, $default);
    }

    public function forgetParam(string $key): self
    {
        data_forget($this->params, $key);

        return $this;
    }

    public function flushState(): void
    {
        $this->params = [];
        $this->name = '';
    }

    public function to(string $name = '', array $params = []): string
    {
        return route(
            $this->getName($name),
            $this->getParams($params)
        );
    }

    public function asyncMethod(
        string $method,
        ?string $message = null,
        array $params = [],
        ?Page $page = null,
        ?ResourceContract $resource = null
    ): string {
        return $this->withPage($page)->withResource($resource)->to('async.method', [
            'method' => $method,
            'message' => $message,
            ...$params,
        ]);
    }

    public function reactive(
        int|string|null $key = null,
        ?Page $page = null,
        ?ResourceContract $resource = null
    ): string {
        return $this->withPage($page)
            ->withResource($resource)
            ->withResourceItem($key ?? $resource?->getItem()?->getKey())
            ->to('async.reactive');
    }

    public function asyncComponent(
        string $name,
        array $additionally = []
    ): string {
        return $this
            ->withPage()
            ->withResource()
            ->to('async.component', [
                '_component_name' => $name,
                '_parentId' => moonshineRequest()->getParentResourceId(),
                ...$additionally,
            ]);
    }

    public function updateColumn(
        string $resourceUri,
        ?string $pageUri = null,
        int|string|null $resourceItem = null,
        ?string $relation = null,
    ): string {
        return $this->to(
            'column.' . ($relation ? 'relation' : 'resource') . '.update-column',
            [
                'resourceUri' => $resourceUri,
                'pageUri' => $pageUri ?? $this->extractPageUri(),
                'resourceItem' => $this->extractResourceItem($resourceItem),
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
        return $this->to("relation.$action", [
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
        string|Page|null $page = null,
        string|ResourceContract|null $resource = null,
        array $params = [],
        bool $redirect = false,
        ?string $fragment = null,
    ): RedirectResponse|string {
        $targetPage = null;

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
                static fn (Pages $pages): ?Page => $pages->findByUri(
                    $page instanceof Page
                        ? $page->uriKey()
                        : MoonShineRouter::uriKey($page)
                ),
            );
        }

        if (is_null($resource)) {
            $targetPage = $page instanceof Page
                ? $page
                : moonshine()->getPages()->findByClass($page);
        }

        throw_if(
            is_null($targetPage),
            new MoonShineException('Page not exists')
        );

        return $redirect
            ? redirect($targetPage->route($params))
            : $targetPage->route($params);
    }

    public function home(): string
    {
        return route(
            moonshineConfig()->getHomeRoute()
        );
    }

    /**
     * @param  class-string  $class
     */
    public static function uriKey(string $class): string
    {
        return str($class)
            ->classBasename()
            ->kebab()
            ->value();
    }

    public function toString(): string
    {
        return $this->to();
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
