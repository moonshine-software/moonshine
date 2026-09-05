<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use Closure;

/**
 * @template T of FieldContract = FieldContract
 * @template TData = mixed
 */
interface ApplyContract
{
    /**
     * @param T $field
     * @return Closure(TData $data): mixed
     */
    public function apply(FieldContract $field): Closure;
}
