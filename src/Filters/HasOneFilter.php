<?php

declare(strict_types=1);

namespace MoonShine\Filters;

use Illuminate\Contracts\Database\Query\Builder;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultMustBeNull;
use MoonShine\Contracts\Fields\Relationships\HasRelatedValues;
use MoonShine\Contracts\Fields\Relationships\HasRelationship;
use MoonShine\Traits\Fields\WithRelatedValues;

class HasOneFilter extends SelectFilter implements
    HasRelationship,
    HasRelatedValues,
    DefaultMustBeNull
{
    use WithRelatedValues;

    protected function resolveQuery(Builder $query): Builder
    {
        $related = $this->getRelated($query->getModel());

        return $query->whereRelation(
            $this->relation(),
            $related->getKeyName(),
            '=',
            $this->requestValue()
        );
    }
}
