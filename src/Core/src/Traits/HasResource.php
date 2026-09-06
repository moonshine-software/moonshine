<?php

declare(strict_types=1);

namespace MoonShine\Core\Traits;

use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Core\Exceptions\ResourceException;

/**
 * @template T of ResourceContract
 */
trait HasResource
{
    /**
     * @var null|T
     */
    protected ?ResourceContract $resource = null;

    /**
     * @param  T  $resource
     */
    public function setResource(ResourceContract $resource): static
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * @phpstan-assert-if-true T $this->resource
     * @phpstan-assert-if-true T $this->getResource()
     */
    public function hasResource(): bool
    {
        return ! \is_null($this->resource);
    }

    /**
     * @return null|T
     */
    public function getResource(): ?ResourceContract
    {
        return $this->resource;
    }

    /** @return T */
    public function getResourceOrFail(): ResourceContract
    {
        return $this->getResource() ?? throw ResourceException::required();
    }

    protected function validateResource(): void
    {
        if (! $this->hasResource()) {
            throw ResourceException::required();
        }
    }
}
