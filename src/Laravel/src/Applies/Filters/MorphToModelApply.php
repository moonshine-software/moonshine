<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Applies\Filters;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Contracts\UI\ApplyContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Relationships\MorphTo;

class MorphToModelApply implements ApplyContract
{
    /** @param MorphTo $field */
    public function apply(FieldContract $field): Closure
    {
        return static function (Builder $query) use ($field): void {
            $query->whereMorphRelation(
                $field->getRelationName(),
                [$field->getRequestTypeValue()],
                $field->getColumn(),
                '=',
                $field->getRequestValue(),
            );
        };
    }
}
