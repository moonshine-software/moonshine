<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Applies\Filters;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Contracts\UI\ApplyContract;
use MoonShine\Contracts\UI\FieldContract;

class DateModelApply implements ApplyContract
{
    /* @param \MoonShine\UI\Fields\Date $field */
    public function apply(FieldContract $field): Closure
    {
        return static function (Builder $query) use ($field): void {
            $query->whereDate($field->getColumn(), $field->getRequestValue());
        };
    }
}
