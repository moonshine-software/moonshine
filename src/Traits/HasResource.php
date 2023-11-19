<?php

declare(strict_types=1);

namespace MoonShine\Traits;

use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Exceptions\ResourceException;
use Throwable;

trait HasResource
{
    protected ?ResourceContract $resource = null;

    protected ?ResourceContract $parentResource = null;

    public function getParentResource(): ?ResourceContract
    {
        return $this->parentResource;
    }

    public function setParentResource(ResourceContract $resource): static
    {
        $this->parentResource = $resource;

        return $this;
    }

    public function setResource(ResourceContract $resource): static
    {
        $this->resource = $resource;

        return $this;
    }

    public function hasResource(): bool
    {
        return ! is_null($this->resource);
    }

    public function getResource(): ?ResourceContract
    {
        return $this->resource;
    }

    /**
     * @throws Throwable
     */
    protected function validateResource(): void
    {
        throw_if(
            ! $this->hasResource(),
            new ResourceException('Resource is required')
        );
    }
}
