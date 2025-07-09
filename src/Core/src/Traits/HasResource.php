<?php

declare(strict_types=1);

namespace MoonShine\Core\Traits;

use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Core\Exceptions\ResourceException;

/**
 * @template T of ResourceContract|null
 */
trait HasResource
{
    /**
     * @var null|T
     */
    protected ?ResourceContract $resource = null;

    public function setResource(ResourceContract $resource): static
    {
        /**
         * @phpstan-ignore assign.propertyType
         */
        $this->resource = $resource;

        return $this;
    }

    public function hasResource(): bool
    {
        return ! \is_null($this->resource);
    }

    /**
     * @return T
     */
    public function getResource(): ?ResourceContract
    {
        return $this->resource;
    }

    protected function validateResource(): void
    {
        if (! $this->hasResource()) {
            throw ResourceException::required();
        }
    }
}
