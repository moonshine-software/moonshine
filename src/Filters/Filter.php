<?php

namespace Leeto\MoonShine\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Contracts\RenderableContract;
use Leeto\MoonShine\Traits\Fields\FormElement;
use Leeto\MoonShine\Traits\Fields\ShowWhen;
use Leeto\MoonShine\Traits\Fields\WithHtmlAttributes;
use Leeto\MoonShine\Traits\Fields\XModel;
use Leeto\MoonShine\Traits\WithAssets;

abstract class Filter implements RenderableContract
{
    use FormElement, WithHtmlAttributes, WithAssets, ShowWhen, XModel;

    public function name(string $index = null): string
    {
        return $this->prepareName($index, 'filters');
    }

    public function getQuery(Builder $query): Builder
    {
        if($this->hasRelationship() && !$this->belongToOne()) {
            $table = $this->getRelated($query->getModel())->getTable();

            return $this->requestValue()
                ? $query->whereHas($this->relation(), function (Builder $q) use($table) {
                    return $q->whereIn("$table.id", $this->requestValue());
                })
                : $query;
        }

        return $this->requestValue() !== false
            ? $query->where($this->field(), $this->requestValue())
            : $query;
    }

    public function getView(): string
    {
        return 'moonshine::filters.' . static::$view;
    }

    public function formViewValue(Model $item): mixed
    {
        return $this->requestValue();
    }
}
