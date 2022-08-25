<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Filters;

use Illuminate\Database\Eloquent\Builder;
use Leeto\MoonShine\Contracts\Fields\HasRelationship;

class HasOneFilter extends Filter implements HasRelationship
{
    public static string $component = 'HasOneFilter';

    public function getQuery(Builder $query): Builder
    {
        return $this->requestValue()
            ? $query->whereRelation($this->relation(), $this->resourceColumn(), '=', $this->requestValue())
            : $query;
    }
}
