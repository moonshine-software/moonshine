<?php

declare(strict_types=1);

namespace MoonShine\UI\Contracts\Fields;

use Closure;
use MoonShine\UI\Collections\Fields;

interface HasReactivity
{
    public function isReactive(): bool;

    public function getReactiveCallback(Fields $fields, mixed $value, array $values): Fields;

    public function reactive(
        ?Closure $callback = null,
        bool $lazy = false,
        int $debounce = 0,
        int $throttle = 0,
    ): static;
}
