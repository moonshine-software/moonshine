<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Fields\Relationships;

use Closure;

interface HasRelatedValues
{
    public function values(): array;

    public function setValues(array $values): void;

    public function valuesQuery(Closure $callback): static;
}
