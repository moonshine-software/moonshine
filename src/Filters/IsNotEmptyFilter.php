<?php

declare(strict_types=1);

namespace MoonShine\Filters;

use Illuminate\Contracts\Database\Query\Builder;

class IsNotEmptyFilter extends SwitchBooleanFilter
{
    public function name(string $index = null): string
    {
        return "filters[is_not_empty_{$this->column()}]";
    }

    protected function resolveQuery(Builder $query): Builder
    {
        return $this->requestValue()
            ? $query->where(
                fn (Builder $query): Builder => $query->whereNotNull(
                    $this->column()
                )->orWhereNot($this->column(), '')->orWhereNot($this->column(), 0)
            )
            : $query;
    }
}
