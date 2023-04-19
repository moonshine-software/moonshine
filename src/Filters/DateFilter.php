<?php

declare(strict_types=1);

namespace MoonShine\Filters;

use Illuminate\Database\Eloquent\Builder;
use MoonShine\Traits\Fields\DateTrait;

class DateFilter extends TextFilter
{
    use DateTrait;

    protected static string $view = 'moonshine::filters.date';

    protected string $type = 'date';

    protected function resolveQuery(Builder $query): Builder
    {
        return $query->whereDate($this->field(), '=', $this->requestValue());
    }
}
