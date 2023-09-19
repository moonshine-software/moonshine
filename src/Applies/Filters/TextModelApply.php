<?php

declare(strict_types=1);

namespace MoonShine\Applies\Filters;

use Closure;
use MoonShine\Contracts\ApplyContract;
use MoonShine\Fields\Field;
use Illuminate\Contracts\Database\Eloquent\Builder;

class TextModelApply implements ApplyContract
{
    public function apply(Field $field): Closure
    {
        return static function (Builder $query) use ($field): void {
            $query->where($field->column(), 'like', "%{$field->requestValue()}%");
        };
    }
}