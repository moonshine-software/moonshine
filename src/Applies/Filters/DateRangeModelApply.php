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
    public function apply(Field $field): Closure
    {
        return static function (Builder $query) use ($field): void {
            $values = $field->requestValue();

            $query->when(
                $values['from'] ?? null,
                function ($query, $from) use ($field): void {
                    $query->whereDate(
                        $field->column(),
                        '>=',
                        Carbon::parse($from)
                    );
                }
            )->when(
                $values['to'] ?? null,
                function ($query, $to) use ($field): void {
                    $query->whereDate(
                        $field->column(),
                        '<=',
                        Carbon::parse($to)
                    );
                }
            );
        };
    }
}
