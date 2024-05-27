<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Applies\Filters;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Core\Contracts\ApplyContract;
use MoonShine\UI\Fields\Field;

class CheckboxModelApply implements ApplyContract
{
    /* @param \MoonShine\UI\Fields\Checkbox $field */
    public function apply(Field $field): Closure
    {
        return static function (Builder $query) use ($field): void {
            if (! empty($field->requestValue())) {
                $query->where($field->getColumn(), $field->requestValue());
            }
        };
    }
}
