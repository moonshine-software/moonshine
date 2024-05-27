<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Applies\Filters;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Core\Contracts\ApplyContract;
use MoonShine\UI\Fields\Field;

class SelectModelApply implements ApplyContract
{
    /* @param  \MoonShine\UI\Fields\Select  $field */
    public function apply(Field $field): Closure
    {
        return static function (Builder $query) use ($field): void {
            if (filled($field->requestValue())) {
                $query->when(
                    $field->isMultiple(),
                    static fn (Builder $q) => $q->whereIn($field->getColumn(), $field->requestValue()),
                    static fn (Builder $q) => $q->where($field->getColumn(), $field->requestValue()),
                );
            }
        };
    }
}
