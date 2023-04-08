<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Contracts\Fields;

interface RemovableContract
{
    public function removable(): static;

    public function isRemovable(): bool;
}
