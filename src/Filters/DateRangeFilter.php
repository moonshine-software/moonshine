<?php

declare(strict_types=1);

namespace MoonShine\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeArray;

class DateRangeFilter extends DateFilter implements DefaultCanBeArray
{
    protected static string $view = 'moonshine::filters.date-range';

    protected bool $group = true;

    protected function resolveQuery(Builder $query): Builder
    {
        $values = $this->requestValue();

        return $query
            ->when($values['from'] ?? null, function ($query, $fromDate) {
                $query->whereDate($this->field(), '>=', Carbon::parse($fromDate));
            })
            ->when($values['to'] ?? null, function ($query, $toDate) {
                $query->whereDate($this->field(), '<=', Carbon::parse($toDate));
            });
    }
}
