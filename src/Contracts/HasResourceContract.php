<?php

declare(strict_types=1);

namespace MoonShine\Contracts;

use MoonShine\Contracts\Resources\ResourceContract;

interface HasResourceContract
{
    public function setResource(ResourceContract $resource): static;

    public function hasResource(): bool;

    public function getResource(): ResourceContract;
}
