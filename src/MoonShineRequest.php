<?php

declare(strict_types=1);

namespace MoonShine;

use Illuminate\Http\Request;
use MoonShine\Pages\Page;
use MoonShine\Resources\Resource;

class MoonShineRequest extends Request
{
    protected ?Resource $resource = null;

    protected ?Page $page = null;

    public function hasResource(): bool
    {
        return str($this->url())->contains('resource/');
    }

    public function getResource(): Resource
    {
        if ($this->resource instanceof Resource) {
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

        $this->page = $this->getResource()
            ->getPages()
            ->findByUri($this->getPageUri());

        return $this->page;
    }

    public function getResourceUri(): ?string
    {
        return $this->route('resourceUri');
    }

    public function getPageUri(): ?string
    {
        return $this->route('pageUri');
    }
}
