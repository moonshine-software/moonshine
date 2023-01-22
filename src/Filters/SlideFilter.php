<?php

namespace Leeto\MoonShine\Filters;

use Illuminate\Database\Eloquent\Builder;
use Leeto\MoonShine\Traits\Fields\NumberTrait;
use Leeto\MoonShine\Traits\Fields\SlideTrait;

class SlideFilter extends Filter
{
    use NumberTrait;
    use SlideTrait;

    public static string $view = 'moonshine::filters.slide';

    protected array $attributes = ['min', 'max', 'step'];

    public function getQuery(Builder $query): Builder
    {
        if ($this->requestValue() === false) {
            $values = [];
        } else {
            $values = array_filter($this->requestValue(), 'is_numeric');
        }

        return $values
            ? $query->whereBetween($this->field, [$values[$this->fromField], $values[$this->toField]])
            : $query;
    }
}
