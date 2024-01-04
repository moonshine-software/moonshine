<?php

declare(strict_types=1);

namespace MoonShine\Contracts;

use Closure;
use MoonShine\Fields\Fields;

interface HasReactivity
{
    public function isReactive(): bool;

    public function reactiveCallback(Fields $fields, mixed $value, array $values): Fields;

    public function reactive(
        ?Closure $callback = null,
        bool $lazy = false,
        int $debounce = 0,
        int $throttle = 0,
    ): static;
}
