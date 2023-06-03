<?php

declare(strict_types=1);

namespace MoonShine\Filters;

use Illuminate\Contracts\Database\Query\Builder;

class IsEmptyFilter extends SwitchBooleanFilter
{
    public function name(string $index = null): string
    {
        return "filters[is_empty_{$this->field()}]";
    }

    protected function resolveQuery(Builder $query): Builder
    {
        return $this->requestValue()
            ? $query->where(
                fn (Builder $query): Builder => $query->whereNull(
                    $this->field()
                )->orWhere($this->field(), '')->orWhere($this->field(), 0)
            )
            : $query;
    }
}
