<?php

declare(strict_types=1);

namespace MoonShine\UI\Contracts\Components;

use Closure;

interface HasCanSeeContract
{
    public function canSee(Closure $callback): static;

    public function isSee(mixed $data): bool;
}
