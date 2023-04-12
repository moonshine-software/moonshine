<?php

namespace MoonShine\Filters;

use Illuminate\Database\Eloquent\Builder;
use MoonShine\Traits\Fields\NumberTrait;
use MoonShine\Traits\Fields\SlideTrait;

class SlideFilter extends Filter
{
    use NumberTrait;
    use SlideTrait;

    protected string $type = 'number';

    protected static string $view = 'moonshine::filters.slide';

    protected array $attributes = [
        'type',
        'min',
        'max',
        'step',
        'disabled',
        'readonly',
        'required',
    ];

    public function getQuery(Builder $query): Builder
    {
        if ($this->requestValue() === false) {
            $values = [];
        } else {
            $values = array_filter($this->requestValue(), 'is_numeric');
        }

        return $values
            ? $query->where(function (Builder $q) use ($values) {
                $q->where($this->fromField, '>=', $values[$this->fromField])
                    ->where($this->toField, '<=', $values[$this->toField]);
            })
            : $query;
    }
}
