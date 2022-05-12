<?php

namespace Leeto\MoonShine\Filters;


use Illuminate\Database\Eloquent\Builder;
use Leeto\MoonShine\Traits\Fields\DateFieldTrait;

class DateRangeFilter extends BaseFilter
{
    use DateFieldTrait;

    public static string $view = 'date-range';

    public static string $type = 'date';

    protected bool $multiple = true;

    public function getQuery(Builder $query): Builder
    {
        $values = $this->requestValue();

        if($values !== false && collect($values)->filter()->isNotEmpty()) {
            return $query->whereBetween(
                $this->field(),
                collect($values)->map(fn($date) => date('Y-m-d', strtotime($date)))
            );
        }

        return $query;
    }
}