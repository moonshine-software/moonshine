<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Fields\Relationships;

use Closure;
use Illuminate\Database\Eloquent\Model;

interface HasRelatedValues
{
    public function relatedValues(Model $item): array;

    public function values(): array;

    public function setValues(array $values): void;

    public function valuesQuery(Closure $callback): self;
}
