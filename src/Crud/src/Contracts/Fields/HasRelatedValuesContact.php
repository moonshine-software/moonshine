<?php

declare(strict_types=1);

namespace MoonShine\Crud\Contracts\Fields;

use Closure;
use MoonShine\Support\DTOs\Select\Options;

interface HasRelatedValuesContact
{
    public function getValues(): Options;

    /**
     * @param  array<mixed>  $values
     */
    public function setValues(array $values): void;

    public function valuesQuery(Closure $callback): static;
}
