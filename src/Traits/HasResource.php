<?php

declare(strict_types=1);

namespace MoonShine\Traits;

use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Exceptions\ResourceException;
use Throwable;

trait HasResource
{
    protected ?ResourceContract $resource = null;

    public function setResource(ResourceContract $resource): static
    {
        $this->resource = $resource;

        return $this;
    }

    public function hasResource(): bool
    {
        return ! is_null($this->resource);
    }

    /**
     * @throws Throwable
     */
    public function getResource(): ResourceContract
    {
        $this->validateResource();

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
