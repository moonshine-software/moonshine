<?php

declare(strict_types=1);

namespace MoonShine\Contracts;

use Closure;
use MoonShine\UI\Fields\Field;

interface ApplyContract
{
    /**
     * @param  Field  $field
     * @return Closure(mixed $data): mixed
     */
    public function apply(Field $field): Closure;
}
