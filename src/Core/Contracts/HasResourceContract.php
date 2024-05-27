<?php

declare(strict_types=1);

namespace MoonShine\Core\Contracts;

use MoonShine\Core\Contracts\Resources\ResourceContract;

interface HasResourceContract
{
    public function setResource(ResourceContract $resource): static;

    public function hasResource(): bool;

    public function getResource(): ?ResourceContract;
}
