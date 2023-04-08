<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Contracts\Fields\HasFormViewValue;
use Leeto\MoonShine\Fields\FormElement;

abstract class Filter extends FormElement implements HasFormViewValue
{
    public function getQuery(Builder $query): Builder
    {
        if ($this->hasRelationship()) {
            $related = $this->getRelated($query->getModel());

            return $this->requestValue()
                ? $query->whereHas($this->relation(), function (Builder $q) use ($related) {
                    $table = $q->getModel()->getTable();
                    $id = $related->getKeyName();

                    return $q->whereIn(
                        "$table.$id",
                        is_array($this->requestValue())
                            ? $this->requestValue()
                            : [$this->requestValue()]
                    );
                })
                : $query;
        }

        return $this->requestValue() !== false
            ? $query->where($this->field(), $this->requestValue())
            : $query;
    }

    public function name(string $index = null): string
    {
        return $this->prepareName($index, 'filters');
    }

    public function formViewValue(Model $item): mixed
    {
        return $this->requestValue();
    }
}
