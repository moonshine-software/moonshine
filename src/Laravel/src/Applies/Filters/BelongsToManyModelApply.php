<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Applies\Filters;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Contracts\UI\ApplyContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Relationships\ModelRelationField;

class BelongsToManyModelApply implements ApplyContract
{
    /* @param  \MoonShine\Laravel\Fields\Relationships\BelongsToMany  $field */
    public function apply(FieldContract $field): Closure
    {
        return static function (Builder $query) use ($field): void {
            if (! $field instanceof ModelRelationField) {
                return;
            }

            $checkedKeys = $field->getCheckedKeys();

            if (is_null($field->getRelation()) || blank($checkedKeys)) {
                return;
            }

            $query->whereHas(
                $field->getRelationName(),
                static fn (Builder $q) => $q->whereIn(
                    $field->getRelation()?->getQualifiedRelatedPivotKeyName(),
                    $checkedKeys
                )
            );
        };
    }
}
