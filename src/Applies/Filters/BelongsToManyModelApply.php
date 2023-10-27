<?php

declare(strict_types=1);

namespace MoonShine\Applies\Filters;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Contracts\ApplyContract;
use MoonShine\Fields\Field;
use MoonShine\Fields\Relationships\ModelRelationField;

class BelongsToManyModelApply implements ApplyContract
{
    /* @param  BelongsToMany  $field */
    public function apply(Field $field): Closure
    {
        return static function (Builder $query) use ($field): void {
            if (! $field instanceof ModelRelationField) {
                return;
            }

            $query->whereHas(
                $field->getRelationName(),
                function (Builder $q) use ($field): Builder {
                    if (is_null($field->getRelation())) {
                        return $q;
                    }

                    $table = $field->getRelation()->getTable();
                    $id = $field->getRelation()->getRelatedPivotKeyName();

                    $values = array_filter($field->requestValue());

                    return $q->whereIn(
                        "$table.$id",
                        $field->isSelectMode()
                            ? $values
                            : array_keys($values)
                    );
                }
            );
        };
    }
}
