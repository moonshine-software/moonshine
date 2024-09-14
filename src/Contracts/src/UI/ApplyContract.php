<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use Closure;

/**
 * @template T of FieldContract
 */
interface ApplyContract
{
    /**
     * @param T $field
     * @return Closure(mixed $data): mixed
     */
    public function apply(FieldContract $field): Closure;
}
