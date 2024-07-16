<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Applies\Filters;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Contracts\UI\ApplyContract;
use MoonShine\Contracts\UI\FieldContract;

class RangeModelApply implements ApplyContract
{
    /* @param \MoonShine\UI\Fields\Range $field */
    public function apply(FieldContract $field): Closure
    {
        return static function (Builder $query) use ($field): void {
            $values = $field->getRequestValue();

            $query->when(
                $values['from'] ?? null,
                static function ($query, $from) use ($field): void {
                    $query->where($field->getColumn(), '>=', $from);
                }
            )->when(
                $values['to'] ?? null,
                static function ($query, $to) use ($field): void {
                    $query->where($field->getColumn(), '<=', $to);
                }
            );
        };
    }
}
