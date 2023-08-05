<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Closure;

class PasswordRepeat extends Password
{
    protected function resolveOnApply(): ?Closure
    {
        return static fn ($item) => $item;
    }
}
