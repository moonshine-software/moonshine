<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Closure;

class PasswordRepeat extends Password
{
    protected function resolveOnSave(): ?Closure
    {
        return static fn ($item) => $item;
    }
}
