<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Contracts\Fields;

use Closure;
use MoonShine\Support\DTOs\Select\Options;

interface HasRelatedValues
{
    public function getValues(): Options;

    public function setValues(array $values): void;

    public function valuesQuery(Closure $callback): static;
}
