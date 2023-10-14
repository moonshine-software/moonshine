<?php

declare(strict_types=1);

namespace MoonShine;

use Illuminate\Http\Request;
use MoonShine\Components\MoonshineComponent;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Decorations\Fragment;
use MoonShine\Http\Middleware\Authenticate;
use MoonShine\Pages\Page;
use Throwable;

class MoonShineRequest extends Request
{
    protected ?ResourceContract $resource = null;

    protected ?Page $page = null;

    public function hasResource(): bool
    {
        return ! is_null($this->getResource());
    }

    public function getResource(): ?ResourceContract
    {
        if ($this->resource instanceof ResourceContract) {
            return $this->resource->boot();
        }

        $this->resource = MoonShine::getResourceFromUriKey(
            $this->getResourceUri()
        );

        return $this->resource->boot();
    }

    public function getPage(): Page
    {
        if ($this->page instanceof Page) {
            return $this->page;
        }

        if ($this->hasResource()) {
            $this->page = $this->getResource()
                ?->getPages()
                ?->findByUri($this->getPageUri());
        } else {
            $this->page = MoonShine::getPageFromUriKey(
                $this->getPageUri()
            );
        }

        if (is_null($this->page)) {
            oops404();
        }

        return $this->page;
    }

    /**
     * @throws Throwable
     */
    //TODO replace return after decoration refactoring
    public function getPageComponent(string $name): null|MoonshineComponent|Fragment
    {
        return $this->getPage()
            ->getComponents()
            ->findByName($name);
    }

    public function getResourceUri(): ?string
    {
        return $this->route('resourceUri');
    }

    public function getParentResourceId(): ?string
    {
        return $this->route('parentId');
    }

    public function getParentRelationName(): ?string
    {
        return
            is_null($parentResource = $this->getParentResourceId())
                ? null
                : explode('-', $parentResource)[0] ?? null;
    }

    public function getParentRelationId(): int|string|null
    {
        return
            is_null($parentResource = $this->getParentResourceId())
                ? null
                : explode('-', $parentResource)[1] ?? null;
    }

    public function getPageUri(): ?string
    {
        return $this->route('pageUri');
    }

    public function onResourceRoute(): bool
    {
        return str($this->url())->contains('resource/');
    }

    public function getFragmentLoad(): ?string
    {
        return $this->get('_fragment-load');
    }

    public function isFragmentLoad(?string $name = null): bool
    {
        $fragment = $this->getFragmentLoad();

        if (! is_null($fragment) && ! is_null($name)) {
            return $fragment === $name;
        }

        return ! is_null($fragment);
    }

    public function isMoonShineRequest(): bool
    {
        return in_array(
            Authenticate::class,
            $this->route()?->gatherMiddleware() ?? [],
            true
        );
    }
}
