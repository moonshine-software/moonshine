<?php

namespace Leeto\MoonShine\Filters;


use Illuminate\Database\Eloquent\Builder;
use Leeto\MoonShine\Traits\Fields\NumberFieldTrait;

class SlideFilter extends BaseFilter
{
    use NumberFieldTrait;

    public static string $view = 'slide';

    public function getQuery(Builder $query): Builder
    {
        return $this->requestValue()
            ? $query->whereBetween('phone', array_values($this->requestValue()))
            : $query;
    }
}