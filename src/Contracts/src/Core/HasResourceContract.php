<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core;

/**
 * @template T of ResourceContract = ResourceContract
 */
interface HasResourceContract
{
    /**
     * @param  T $resource
     */
    public function setResource(ResourceContract $resource): static;

    public function hasResource(): bool;

    /**
     * @return null|T
     */
    public function getResource(): ?ResourceContract;
}
