<?php

declare(strict_types=1);

namespace MoonShine\Applies\Filters;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Contracts\ApplyContract;
use MoonShine\Fields\Field;

class BelongsToManyModelApply implements ApplyContract
{
    public function apply(Field $field): Closure
    {
        return static function (Builder $query) use ($field): void {
            $query->whereHas(
                $field->getRelationName(),
                function (Builder $q) use ($field): Builder {
                    $table = $field->getRelation()->getTable();
                    $id = $field->getRelation()->getRelatedPivotKeyName();

                    return $q->whereIn(
                        "$table.$id",
                        is_array($field->requestValue())
                            ? $field->requestValue()
                            : [$field->requestValue()]
                    );
                }
            );
        };
    }
}
