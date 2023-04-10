<?php

declare(strict_types=1);

namespace MoonShine\Filters;

use Illuminate\Database\Eloquent\Builder;
use MoonShine\Contracts\Fields\Relationships\HasRelationship;
use MoonShine\Contracts\Fields\Relationships\OneToOneRelation;
use MoonShine\Traits\Fields\WithRelationship;

class HasOneFilter extends SelectFilter implements HasRelationship, OneToOneRelation
{
    use WithRelationship;

    public function getQuery(Builder $query): Builder
    {
        return $this->requestValue()
            ? $query->whereRelation($this->relation(), 'id', '=', $this->requestValue())
            : $query;
    }
}
