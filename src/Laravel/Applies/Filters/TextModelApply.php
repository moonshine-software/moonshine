<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Applies\Filters;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Laravel\Support\DBOperators;
use MoonShine\UI\Contracts\ApplyContract;
use MoonShine\UI\Fields\Field;

class TextModelApply implements ApplyContract
{
    /* @param \MoonShine\UI\Fields\Text $field */
    public function apply(Field $field): Closure
    {
        return static function (Builder $query) use ($field): void {
            $query->where($field->getColumn(), DBOperators::byModel($query->getModel())->like(), "%{$field->getRequestValue()}%");
        };
    }
}
