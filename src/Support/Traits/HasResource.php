<?php

declare(strict_types=1);

namespace MoonShine\Support\Traits;

use MoonShine\Core\Contracts\ResourceContract;
use MoonShine\Core\Exceptions\ResourceException;
use Throwable;

/**
 * @template-covariant T of ResourceContract
 * @template-covariant PT of ResourceContract
 */
trait HasResource
{
    protected ?ResourceContract $resource = null;

    protected ?ResourceContract $parentResource = null;

    /** @return ResourceContract */
    public function getParentResource(): ?ResourceContract
    {
        return $this->parentResource;
    }

    /**
     * @param  ResourceContract  $resource
     * @return ResourceContract
     */
    public function setParentResource(ResourceContract $resource): static
    {
        $this->parentResource = $resource;

        return $this;
    }

    /**
     * @param  ResourceContract  $resource
     * @return ResourceContract
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

    /** @return ResourceContract */
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
