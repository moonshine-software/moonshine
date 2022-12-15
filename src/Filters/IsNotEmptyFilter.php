<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Filters;

use Illuminate\Database\Eloquent\Builder;
use Leeto\MoonShine\Traits\Fields\BooleanTrait;

class IsNotEmptyFilter extends Filter
{

    use BooleanTrait;

    public static string $view = 'moonshine::filters.switch';

    public function getQuery(Builder $query): Builder
    {

        return $this->requestValue()
            ? $query->where(fn(Builder $query) => $query->whereNotNull($this->field())->orWhereNot($this->field(), '')->orWhereNot($this->field(), 0))
            : $query;

    }

    public function name(string $index = null): string
    {
        return "filters[is_not_empty__{$this->field()}]";
    }

}
