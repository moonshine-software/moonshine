<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Contracts\Fields;

interface Removable
{
    public function removable(): static;

    public function isRemovable(): bool;
}
