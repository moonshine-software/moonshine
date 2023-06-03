<?php

namespace MoonShine\Filters;

use Illuminate\Contracts\Database\Query\Builder;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeArray;
use MoonShine\Contracts\Fields\HasDefaultValue;
use MoonShine\Traits\Fields\NumberTrait;
use MoonShine\Traits\Fields\SlideTrait;
use MoonShine\Traits\Fields\WithDefaultValue;

class SlideFilter extends Filter implements
    HasDefaultValue,
    DefaultCanBeArray
{
    use NumberTrait;
    use SlideTrait;
    use WithDefaultValue;

    protected static string $view = 'moonshine::filters.slide';
    public string $type = 'number';
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

        return $query->where(function (Builder $query) use ($values): void {
            $query
                ->where($this->field(), '>=', $values[$this->fromField])
                ->where($this->field(), '<=', $values[$this->toField]);
        });
    }
}
