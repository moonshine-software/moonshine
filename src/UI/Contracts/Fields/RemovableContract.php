<?php

declare(strict_types=1);

namespace MoonShine\UI\Contracts\Fields;

use Closure;

interface RemovableContract
{
    public function removable(
        Closure|bool|null $condition = null,
        array $attributes = [],
    ): static;

    public function isRemovable(): bool;
}
