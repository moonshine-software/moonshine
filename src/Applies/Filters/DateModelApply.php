<?php

declare(strict_types=1);

namespace MoonShine\Applies\Filters;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Contracts\ApplyContract;
use MoonShine\Fields\Field;

class DateModelApply implements ApplyContract
{
    /* @param \MoonShine\Fields\Date $field */
    public function apply(Field $field): Closure
    {
        return static function (Builder $query) use ($field): void {
            $query->whereDate($field->column(), $field->requestValue());
        };
    }
}
