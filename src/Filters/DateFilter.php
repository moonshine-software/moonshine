<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Filters;

use Illuminate\Database\Eloquent\Builder;
use Leeto\MoonShine\Traits\Fields\DateTrait;

class DateFilter extends Filter
{
    use DateTrait;

    public static string $view = 'date';

    public static string $type = 'date';

    public function getQuery(Builder $query): Builder
    {
        return $this->requestValue()
            ? $query->whereDate($this->field(), '=', $this->requestValue())
            : $query;
    }
}
