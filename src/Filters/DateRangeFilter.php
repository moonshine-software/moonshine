<?php

declare(strict_types=1);

namespace MoonShine\Filters;

use Illuminate\Contracts\Database\Query\Builder;
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
            ->when($values['from'] ?? null, function ($query, $fromDate): void {
                $query->whereDate(
                    $this->column(),
                    '>=',
                    Carbon::parse($fromDate)
                );
            })
            ->when($values['to'] ?? null, function ($query, $toDate): void {
                $query->whereDate($this->column(), '<=', Carbon::parse($toDate));
            });
    }
}
