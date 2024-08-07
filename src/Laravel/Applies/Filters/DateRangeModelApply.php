<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Applies\Filters;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use MoonShine\Contracts\UI\ApplyContract;
use MoonShine\Contracts\UI\FieldContract;

class DateRangeModelApply implements ApplyContract
{
    /* @param \MoonShine\UI\Fields\DateRange $field */
    public function apply(FieldContract $field): Closure
    {
        return static function (Builder $query) use ($field): void {
            $values = $field->getRequestValue();

            $condition = $field->getAttribute('type') === 'datetime-local' ? 'where' : 'whereDate';

            $query->when(
                $values['from'] ?? null,
                static fn ($query, $from) => $query->{$condition}(
                    $field->getColumn(),
                    '>=',
                    Carbon::parse($from)
                )
            )->when(
                $values['to'] ?? null,
                static fn ($query, $to) => $query->$condition(
                    $field->getColumn(),
                    '<=',
                    Carbon::parse($to)
                )
            );
        };
    }
}
