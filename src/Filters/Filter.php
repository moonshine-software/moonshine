<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Filters;

use Illuminate\Database\Eloquent\Builder;
use Leeto\MoonShine\Fields\Field;

abstract class Filter extends Field
{
    public function name(string $index = null): string
    {
        return $this->prepareName($index, 'filters');
    }

    public function getQuery(Builder $query): Builder
    {
        if ($this->hasRelationship()) {
            $related = $query->getModel()->{$this->relation()}->getRelated();

            return $this->requestValue()
                ? $query->whereHas($this->relation(), function (Builder $q) use ($related) {
                    return $q->whereIn("{$related->getTable()}.{$related->getKeyName()}", $this->requestValue());
                })
                : $query;
        }

        return $this->requestValue() !== false
            ? $query->where($this->column(), $this->requestValue())
            : $query;
    }

    public function value(): mixed
    {
        return $this->requestValue();
    }
}
