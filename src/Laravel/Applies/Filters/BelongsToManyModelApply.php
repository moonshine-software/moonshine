<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Applies\Filters;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Laravel\Fields\Relationships\ModelRelationField;
use MoonShine\UI\Contracts\ApplyContract;
use MoonShine\UI\Fields\Field;

class BelongsToManyModelApply implements ApplyContract
{
    /* @param  \MoonShine\Laravel\Fields\Relationships\BelongsToMany  $field */
    public function apply(Field $field): Closure
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
