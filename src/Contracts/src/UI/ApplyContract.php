<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use Closure;

interface ApplyContract
{
    /**
     * @return Closure(mixed $data): mixed
     */
    public function apply(FieldContract $field): Closure;
}
