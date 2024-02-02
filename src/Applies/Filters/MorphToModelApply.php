<?php

declare(strict_types=1);

namespace MoonShine\Applies\Filters;

use MoonShine\Fields\Relationships\MorphTo;
use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Contracts\ApplyContract;
use MoonShine\Fields\Field;

class MorphToModelApply implements ApplyContract
{
    /** @param MorphTo $field */
    public function apply(Field $field): Closure
    {
        return static function (Builder $query) use ($field): void {
            $query->whereMorphRelation(
                $field->getRelationName(),
                [$field->requestTypeValue()],
                $field->column(),
                '=',
                $field->requestValue(),
            );
        };
    }
}
