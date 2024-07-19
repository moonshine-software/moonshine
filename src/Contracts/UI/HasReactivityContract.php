<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use Closure;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;

interface HasReactivityContract
{
    public function isReactive(): bool;

    public function getReactiveCallback(FieldsContract $fields, mixed $value, array $values): FieldsContract;

    public function reactive(
        ?Closure $callback = null,
        bool $lazy = false,
        int $debounce = 0,
        int $throttle = 0,
    ): static;
}
