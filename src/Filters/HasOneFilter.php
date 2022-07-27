<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Filters;

use Illuminate\Database\Eloquent\Builder;
use Leeto\MoonShine\Contracts\Fields\HasRelationshipContract;
use Leeto\MoonShine\Traits\Fields\WithRelationshipsTrait;
use Leeto\MoonShine\Traits\Fields\SearchableTrait;

class HasOneFilter extends Filter implements HasRelationshipContract
{
    use SearchableTrait, WithRelationshipsTrait;

    public static bool $toOne = true;

    public static string $view = 'select';

    public function getQuery(Builder $query): Builder
    {
        return $this->requestValue()
            ? $query->whereRelation($this->relation(), 'id', '=', $this->requestValue())
            : $query;
    }
}
