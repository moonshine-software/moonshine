<?php

declare(strict_types=1);

namespace MoonShine\Filters;

use Illuminate\Database\Eloquent\Builder;

class IsEmptyFilter extends SwitchBooleanFilter
{
    public function getQuery(Builder $query): Builder
    {
        return $this->requestValue()
            ? $query->where(fn (Builder $query) => $query->whereNull($this->field())->orWhere($this->field(), '')->orWhere($this->field(), 0))
            : $query;
    }

    public function name(string $index = null): string
    {
        return "filters[is_empty_{$this->field()}]";
    }
}
