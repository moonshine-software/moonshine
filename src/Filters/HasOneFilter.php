<?php

namespace Leeto\MoonShine\Filters;


use Illuminate\Database\Eloquent\Builder;
use Leeto\MoonShine\Contracts\Fields\FieldHasRelationContract;
use Leeto\MoonShine\Traits\Fields\FieldWithRelationshipsTrait;
use Leeto\MoonShine\Traits\Fields\SearchableSelectFieldTrait;

class HasOneFilter extends BaseFilter implements FieldHasRelationContract
{
    use SearchableSelectFieldTrait, FieldWithRelationshipsTrait;

    public static bool $toOne = true;

    public static string $view = 'select';

    public function getQuery(Builder $query): Builder
    {
        return $this->requestValue()
            ? $query->whereRelation($this->relation(), 'id', '=', $this->requestValue())
            : $query;
    }
}