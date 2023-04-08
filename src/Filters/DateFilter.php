<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Filters;

use Illuminate\Database\Eloquent\Builder;
use Leeto\MoonShine\Traits\Fields\DateTrait;

class DateFilter extends TextFilter
{
    use DateTrait;

    protected static string $view = 'moonshine::filters.date';

    protected string $type = 'date';

    public function getQuery(Builder $query): Builder
    {
        return $this->requestValue()
            ? $query->whereDate($this->field(), '=', $this->requestValue())
            : $query;
    }
}
