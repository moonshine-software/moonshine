<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Filters;

use Illuminate\Database\Eloquent\Builder;
use Leeto\MoonShine\Traits\Fields\DateTrait;

class DateRangeFilter extends Filter
{
    use DateTrait;

    public static string $component = 'DateRange';

    protected bool $multiple = true;

    public function getQuery(Builder $query): Builder
    {
        $values = $this->requestValue();

        if ($values !== false && collect($values)->filter()->isNotEmpty()) {
            return $query->whereBetween(
                $this->column(),
                collect($values)->map(fn($date) => date('Y-m-d', strtotime($date)))
            );
        }

        return $query;
    }
}
