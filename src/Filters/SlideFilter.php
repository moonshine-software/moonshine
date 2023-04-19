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

    protected function resolveQuery(Builder $query): Builder
    {
        $values = array_filter($this->requestValue(), 'is_numeric');

        return $query->where(function (Builder $query) use ($values) {
            $query
                ->where($this->fromField, '>=', $values[$this->fromField])
                ->where($this->toField, '<=', $values[$this->toField]);
        });
    }
}
