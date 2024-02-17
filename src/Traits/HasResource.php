<?php

declare(strict_types=1);

namespace MoonShine\Traits;

use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Exceptions\ResourceException;
use Throwable;

/**
 * @template-covariant T of ResourceContract
 * @template-covariant PT of ResourceContract
 */
trait HasResource
{
    protected ?ResourceContract $resource = null;

    protected ?ResourceContract $parentResource = null;

    /** @return PT */
    public function getParentResource(): ?ResourceContract
    {
        return $this->parentResource;
    }

    /**
     * @param  PT  $resource
     * @return PT
     */
    public function setParentResource(ResourceContract $resource): static
    {
        $this->parentResource = $resource;

        return $this;
    }

    /**
     * @param  T  $resource
     * @return T
     */
    public function setResource(ResourceContract $resource): static
    {
        $this->resource = $resource;

        return $this;
    }

    public function hasResource(): bool
    {
        return ! is_null($this->resource);
    }

    /** @return T */
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
            ResourceException::required()
        );
    }
}
