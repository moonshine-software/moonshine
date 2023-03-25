<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Contracts\Fields\Relationships;

interface HasResourceMode
{
    public function resourceMode(): static;

    public function isResourceMode(): bool;
}
