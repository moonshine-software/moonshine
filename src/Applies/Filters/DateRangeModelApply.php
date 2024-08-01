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
                function ($query, $from) use ($field, $condition): void {
                    $query->$condition(
                        $field->column(),
                        '>=',
                        Carbon::parse($from)
                    );
                }
            )->when(
                $values['to'] ?? null,
                function ($query, $to) use ($field, $condition): void {
                    $query->$condition(
                        $field->column(),
                        '<=',
                        Carbon::parse($to)
                    );
                }
            );
        };
    }
}
