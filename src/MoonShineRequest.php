<?php

declare(strict_types=1);

namespace MoonShine;

use Illuminate\Http\Request;
use MoonShine\Components\MoonshineComponent;
use MoonShine\Contracts\Resources\ResourceContract;
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
            return $this->resource;
        }

        $this->resource = MoonShine::getResourceFromUriKey(
            $this->getResourceUri()
        );

        return $this->resource;
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

        return $this->page;
    }

    /**
     * @throws Throwable
     */
    public function getPageComponent(string $name): ?MoonshineComponent
    {
        return $this->getPage()
            ->getComponents()
            ->findByName($name);
    }

    public function getResourceUri(): ?string
    {
        return $this->route('resourceUri');
    }

    public function getPageUri(): ?string
    {
        return $this->route('pageUri');
    }

    public function onResourceRoute(): bool
    {
        return str($this->url())->contains('resource/');
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
