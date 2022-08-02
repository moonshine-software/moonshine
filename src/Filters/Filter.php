<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Filters;

use Illuminate\Database\Eloquent\Builder;
use Leeto\MoonShine\FormElement;
use Leeto\MoonShine\Traits\Fields\ShowWhen;
use Leeto\MoonShine\Traits\Makeable;

abstract class Filter extends FormElement
{
    use Makeable, ShowWhen;

    public function name(string $index = null): string
    {
        return $this->prepareName($index, 'filters');
    }

    public function getQuery(Builder $query): Builder
    {
        if ($this->hasRelationship() && !$this->belongToOne()) {
            $table = $this->getRelated($query->getModel())->getTable();

            return $this->requestValue()
                ? $query->whereHas($this->relation(), function (Builder $q) use ($table) {
                    return $q->whereIn("$table.id", $this->requestValue());
                })
                : $query;
        }

        return $this->requestValue() !== false
            ? $query->where($this->field(), $this->requestValue())
            : $query;
    }

    public function value(): mixed
    {
        return $this->requestValue();
    }
}
