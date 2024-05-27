<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Applies\Filters;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Contracts\ApplyContract;
use MoonShine\UI\Fields\Field;

class RangeModelApply implements ApplyContract
{
    /* @param \MoonShine\UI\Fields\Range $field */
    public function apply(Field $field): Closure
    {
        return static function (Builder $query) use ($field): void {
            $values = $field->requestValue();

            $query->when(
                $values['from'] ?? null,
                function ($query, $from) use ($field): void {
                    $query->where($field->getColumn(), '>=', $from);
                }
            )->when(
                $values['to'] ?? null,
                function ($query, $to) use ($field): void {
                    $query->where($field->getColumn(), '<=', $to);
                }
            );
        };
    }
}
