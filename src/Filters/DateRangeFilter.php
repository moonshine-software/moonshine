<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Leeto\MoonShine\Traits\Fields\DateTrait;

class DateRangeFilter extends Filter
{
    use DateTrait;

    public static string $view = 'moonshine::filters.date-range';

    public static string $type = 'date';

    protected bool $group = true;

    public function getQuery(Builder $query): Builder
    {
        $values = $this->requestValue();

        return $query
            ->when($values['from'] ?? null, function ($query, $fromDate) {
                $query->whereDate($this->field, '>=', Carbon::parse($fromDate));
            })
            ->when($values['to'] ?? null, function ($query, $toDate) {
                $query->whereDate($this->field, '<=', Carbon::parse($toDate));
            });
    }
}
