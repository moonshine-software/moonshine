<?php

declare(strict_types=1);

namespace MoonShine\Filters;

use Illuminate\Contracts\Database\Query\Builder;
use MoonShine\Traits\Fields\DateTrait;

class DateFilter extends TextFilter
{
    use DateTrait;

    protected static string $view = 'moonshine::filters.date';

    public string $type = 'date';

    protected function resolveQuery(Builder $query): Builder
    {
        return $query->whereDate($this->column(), '=', $this->requestValue());
    }
}
