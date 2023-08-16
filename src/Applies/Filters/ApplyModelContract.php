<?php

declare(strict_types=1);

namespace MoonShine\Applies\Filters;

use Closure;
use MoonShine\Fields\Field;

interface ApplyModelContract
{
    public function apply(Field $field): Closure;
}