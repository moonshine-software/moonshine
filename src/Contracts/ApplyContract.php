<?php

declare(strict_types=1);

namespace MoonShine\Contracts;

use Closure;
use MoonShine\Fields\Field;

interface ApplyContract
{
    public function apply(Field $field): Closure;
}
