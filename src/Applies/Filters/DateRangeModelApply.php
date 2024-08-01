<?php

declare(strict_types=1);

namespace MoonShine\Applies\Filters;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use MoonShine\Contracts\ApplyContract;
use MoonShine\Fields\Field;

class DateRangeModelApply implements ApplyContract
{
    /* @param \MoonShine\Fields\DateRange $field */
    public function apply(Field $field): Closure
    {
        return static function (Builder $query) use ($field): void {
            $values = $field->requestValue();

            $condition = $field->type() === 'datetime-local' ? 'where' : 'whereDate';

            $query->when(
                $values['from'] ?? null,
                fn ($query, $from) => $query->$condition(
                    $field->column(),
                    '>=',
                    Carbon::parse($from)
                )
            )->when(
                $values['to'] ?? null,
                fn ($query, $to) => $query->$condition(
                    $field->column(),
                    '<=',
                    Carbon::parse($to)
                )
            );
        };
    }
}
