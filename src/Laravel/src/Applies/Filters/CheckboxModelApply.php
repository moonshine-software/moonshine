<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Applies\Filters;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Contracts\UI\ApplyContract;
use MoonShine\Contracts\UI\FieldContract;

class CheckboxModelApply implements ApplyContract
{
    /* @param \MoonShine\UI\Fields\Checkbox $field */
    public function apply(FieldContract $field): Closure
    {
        return static function (Builder $query) use ($field): void {
            if (! empty($field->getRequestValue())) {
                $query->where($field->getColumn(), $field->getRequestValue());
            }
        };
    }
}
