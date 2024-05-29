<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Applies\Filters;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Contracts\ApplyContract;
use MoonShine\UI\Fields\Field;

class DateModelApply implements ApplyContract
{
    /* @param \MoonShine\UI\Fields\Date $field */
    public function apply(Field $field): Closure
    {
        return static function (Builder $query) use ($field): void {
            $query->whereDate($field->getColumn(), $field->getRequestValue());
        };
    }
}
