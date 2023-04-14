<?php

declare(strict_types=1);

namespace MoonShine\Filters;

use Illuminate\Database\Eloquent\Builder;
use MoonShine\Contracts\Fields\Relationships\HasRelatedValues;
use MoonShine\Contracts\Fields\Relationships\HasRelationship;
use MoonShine\Traits\Fields\WithRelatedValues;

class HasOneFilter extends SelectFilter implements
    HasRelationship,
    HasRelatedValues
{
    use WithRelatedValues;

    public function getQuery(Builder $query): Builder
    {
        $related = $this->getRelated($query->getModel());

        return $this->requestValue()
            ? $query->whereRelation($this->relation(), $related->getKeyName(), '=', $this->requestValue())
            : $query;
    }
}
