<?php

declare(strict_types=1);

namespace MoonShine;

use Illuminate\Http\Request;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Http\Middleware\Authenticate;
use MoonShine\Pages\Page;

class MoonShineRequest extends Request
{
    protected ?ResourceContract $resource = null;

    protected ?Page $page = null;

    public function onResourceRoute(): bool
    {
        return str($this->url())->contains('resource/');
    }

    public function hasResource(): bool
    {
        return ! is_null($this->getResource());
    }

    /**
     * @return ResourceContract|null
     */
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

    public function getResourceUri(): ?string
    {
        return $this->route('resourceUri', request('_resourceUri'));
    }

    public function getPageUri(): ?string
    {
        return $this->route('pageUri');
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
