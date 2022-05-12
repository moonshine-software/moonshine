<?php

namespace Leeto\MoonShine\Filters;

use Illuminate\Database\Eloquent\Builder;
use Leeto\MoonShine\Traits\Fields\DateFieldTrait;

class DateFilter extends BaseFilter
{
    use DateFieldTrait;

    public static string $view = 'date';

    public static string $type = 'date';

    public function getQuery(Builder $query): Builder
    {
        return $this->requestValue()
            ? $query->whereDate($this->field(), '=', $this->requestValue())
            : $query;
    }
}