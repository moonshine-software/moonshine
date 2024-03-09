<?php

declare(strict_types=1);

namespace MoonShine\Applies\Filters;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Contracts\ApplyContract;
use MoonShine\Fields\Field;

class SelectModelApply implements ApplyContract
{
    /* @param  \MoonShine\Fields\Select  $field */
    public function apply(Field $field): Closure
    {
        return static function (Builder $query) use ($field): void {
            if (! empty($field->requestValue())) {
                $query->when(
                    $field->isMultiple(),
                    static fn (Builder $q) => $q->whereIn($field->column(), $field->requestValue()),
                    static fn (Builder $q) => $q->where($field->column(), $field->requestValue()),
                );
            }
        };
    }
}
