<?php

declare(strict_types=1);

namespace MoonShine\Core\Traits;

use MoonShine\Core\Contracts\PageContract;
use MoonShine\Core\Contracts\ResourceContract;

trait NowOn
{
    private ?ResourceContract $nowOnResource = null;

    private ?PageContract $nowOnPage = null;

    private array $nowOnQueryParams = [];

    public function nowOn(
        ?PageContract $page = null,
        ?ResourceContract $resource = null,
        array $params = []
    ): static {
        $this->nowOnPage = $page;
        $this->nowOnResource = $resource;
        $this->nowOnQueryParams = $params;

        return $this;
    }

    public function nowOnPage(PageContract $page): static
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

    public function getNowOnPage(): ?PageContract
    {
        return $this->nowOnPage;
    }

    public function getNowOnQueryParams(): array
    {
        return $this->nowOnQueryParams;
    }
}
