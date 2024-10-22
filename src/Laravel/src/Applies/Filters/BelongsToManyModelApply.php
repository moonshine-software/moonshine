<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Applies\Filters;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Contracts\UI\ApplyContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Relationships\BelongsToMany;
use MoonShine\Laravel\Fields\Relationships\ModelRelationField;

/**
 * @implements ApplyContract<BelongsToMany>
 */
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

            $relation = $field->getRelation();

            if (\is_null($relation) || blank($checkedKeys)) {
                return;
            }

            $query->whereHas(
                $field->getRelationName(),
                static fn (Builder $q) => $q->whereIn(
                    $relation->getQualifiedRelatedPivotKeyName(),
                    $checkedKeys
                )
            );
        };
    }
}
