<?php

declare(strict_types=1);

namespace MoonShine\Core\Traits;

use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Core\Pages\Page;

trait NowOn
{
    private ?ResourceContract $nowOnResource = null;

    private ?Page $nowOnPage = null;

    private array $nowOnQueryParams = [];

    public function nowOn(
        ?Page $page = null,
        ?ResourceContract $resource = null,
        array $params = []
    ): static {
        $this->nowOnPage = $page;
        $this->nowOnResource = $resource;
        $this->nowOnQueryParams = $params;

        return $this;
    }

    public function nowOnPage(Page $page): static
    {
        $this->nowOnPage = $page;

        return $this;
    }

    public function nowOnResource(ResourceContract $resource): static
    {
        $this->nowOnResource = $resource;

        return $this;
    }

    public function nowOnParams(array $params): static
    {
        $this->nowOnQueryParams = $params;

        return $this;
    }

    public function getNowOnResource(): ?ResourceContract
    {
        return $this->nowOnResource;
    }

    public function getNowOnPage(): ?Page
    {
        return $this->nowOnPage;
    }

    public function getNowOnQueryParams(): array
    {
        return $this->nowOnQueryParams;
    }
}
