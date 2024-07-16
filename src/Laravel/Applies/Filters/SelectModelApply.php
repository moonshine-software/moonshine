<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Applies\Filters;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Contracts\UI\ApplyContract;
use MoonShine\Contracts\UI\FieldContract;

class SelectModelApply implements ApplyContract
{
    /* @param  \MoonShine\UI\Fields\Select  $field */
    public function apply(FieldContract $field): Closure
    {
        return static function (Builder $query) use ($field): void {
            if (filled($field->getRequestValue())) {
                $query->when(
                    $field->isMultiple(),
                    static fn (Builder $q) => $q->whereIn($field->getColumn(), $field->getRequestValue()),
                    static fn (Builder $q) => $q->where($field->getColumn(), $field->getRequestValue()),
                );
            }
        };
    }
}
