<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Fields\Relationships;

interface HasResourceMode
{
    public function resourceMode(): static;

    public function isResourceMode(): bool;
}
