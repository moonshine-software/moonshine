<?php

namespace Leeto\MoonShine\Filters;

use Illuminate\Database\Eloquent\Builder;
use Leeto\MoonShine\Traits\Fields\NumberTrait;
use Leeto\MoonShine\Traits\Fields\SlideTrait;

class SlideFilter extends Filter
{
    use NumberTrait;
    use SlideTrait;

    public static string $component = 'SlideFilter';

    protected array $attributes = ['min', 'max', 'step'];

    public function getQuery(Builder $query): Builder
    {
        if ($this->requestValue() === false) {
            $values = [];
        } else {
            $values = array_filter($this->requestValue());
        }

        return $values
            ? $query->where($this->from(), '<=', $values[$this->from()])
                ->where($this->to(), '>=', $values[$this->to()])
            : $query;
    }
}
