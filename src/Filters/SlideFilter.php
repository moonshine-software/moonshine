<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Filters;

use Illuminate\Database\Eloquent\Builder;
use Leeto\MoonShine\Traits\Fields\NumberTrait;

class SlideFilter extends Filter
{
    use NumberTrait;

    public static string $view = 'slide';

    public function getQuery(Builder $query): Builder
    {
        return $this->requestValue()
            ? $query->whereBetween('phone', array_values($this->requestValue()))
            : $query;
    }
}
