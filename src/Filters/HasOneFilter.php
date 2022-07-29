<?php

namespace Leeto\MoonShine\Filters;


use Illuminate\Database\Eloquent\Builder;
use Leeto\MoonShine\Contracts\Fields\Relationships\HasRelationshipContract;
use Leeto\MoonShine\Contracts\Fields\Relationships\OneToOneRelationshipContract;
use Leeto\MoonShine\Traits\Fields\CanBeMultiple;
use Leeto\MoonShine\Traits\Fields\Searchable;
use Leeto\MoonShine\Traits\Fields\WithRelationship;

class HasOneFilter extends Filter implements HasRelationshipContract, OneToOneRelationshipContract
{
    use Searchable, WithRelationship, CanBeMultiple;

    public static string $view = 'select';

    public function getQuery(Builder $query): Builder
    {
        return $this->requestValue()
            ? $query->whereRelation($this->relation(), 'id', '=', $this->requestValue())
            : $query;
    }
}
