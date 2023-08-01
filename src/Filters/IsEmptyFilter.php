<?php

declare(strict_types=1);

namespace MoonShine\Filters;

use Illuminate\Contracts\Database\Query\Builder;

class IsEmptyFilter extends SwitchBooleanFilter
{
    public function name(string $index = null): string
    {
        return "filters[is_empty_{$this->column()}]";
    }

    protected function resolveQuery(Builder $query): Builder
    {
        return $this->requestValue()
            ? $query->where(
                fn (Builder $query): Builder => $query->whereNull(
                    $this->column()
                )->orWhere($this->column(), '')->orWhere($this->column(), 0)
            )
            : $query;
    }
}
