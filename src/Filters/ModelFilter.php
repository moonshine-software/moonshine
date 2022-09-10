<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Leeto\MoonShine\Traits\Makeable;

final class ModelFilter extends Filter
{
    public function apply(Builder $query): Builder
    {
        return tap($query, function ($query) {
            $this->queryCallback(
                $query,
                $this->fields()->requestValues('filters')
            );
        });
    }
}
