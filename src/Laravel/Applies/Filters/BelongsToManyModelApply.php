<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Applies\Filters;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Contracts\ApplyContract;
use MoonShine\Laravel\Fields\Relationships\ModelRelationField;
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

            $values = array_filter($field->requestValue());

            if (is_null($field->getRelation()) || blank($values)) {
                return;
            }

            $query->whereHas(
                $field->getRelationName(),
                function (Builder $q) use ($field, $values): Builder {
                    $table = $field->getRelation()?->getTable();
                    $id = $field->getRelation()?->getRelatedPivotKeyName();

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
