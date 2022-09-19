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
            $values = array_filter($this->requestValue());
        }

        return $values
            ? $query->where($this->fromField, '<=', $values[$this->fromField])
                ->where($this->toField, '>=', $values[$this->toField])
            : $query;
    }
}
