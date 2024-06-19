<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Applies\Filters;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use MoonShine\UI\Contracts\ApplyContract;
use MoonShine\UI\Fields\Field;

class DateRangeModelApply implements ApplyContract
{
    /* @param \MoonShine\UI\Fields\DateRange $field */
    public function apply(Field $field): Closure
    {
        return static function (Builder $query) use ($field): void {
            $values = $field->getRequestValue();

            $query->when(
                $values['from'] ?? null,
                static function ($query, $from) use ($field): void {
                    $query->whereDate(
                        $field->getColumn(),
                        '>=',
                        Carbon::parse($from)
                    );
                }
            )->when(
                $values['to'] ?? null,
                static function ($query, $to) use ($field): void {
                    $query->whereDate(
                        $field->getColumn(),
                        '<=',
                        Carbon::parse($to)
                    );
                }
            );
        };
    }
}
