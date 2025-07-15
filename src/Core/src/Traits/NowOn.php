<?php

declare(strict_types=1);

namespace MoonShine\Core\Traits;

use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\ResourceContract;

/**
 * @phpstan-ignore trait.unused
 */
trait NowOn
{
    private ?ResourceContract $nowOnResource = null;

    private ?PageContract $nowOnPage = null;

    /**
     * @var  array<string, mixed>
     */
    private array $nowOnQueryParams = [];

    /**
     * @param  array<string, mixed>  $params
     */
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

    /**
     * @param array<string, mixed> $params
     */
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

    /**
     * @return array<string, mixed>
     */
    public function getNowOnQueryParams(): array
    {
        return $this->nowOnQueryParams;
    }
}
