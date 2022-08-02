<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Filters;

use Illuminate\Database\Eloquent\Builder;
use Leeto\MoonShine\Contracts\Fields\Relationships\HasRelationship;
use Leeto\MoonShine\Contracts\Fields\Relationships\OneToOneRelation;
use Leeto\MoonShine\Traits\Fields\WithRelationship;

class HasOneFilter extends Filter implements HasRelationship, OneToOneRelation
{
    use WithRelationship;

    public static string $view = 'moonshine::filters.text';

    public function getQuery(Builder $query): Builder
    {
        return $this->requestValue()
            ? $query->whereRelation($this->relation(), $this->resourceTitleField(), '=', $this->requestValue())
            : $query;
    }
}
