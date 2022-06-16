<?php

namespace Leeto\MoonShine\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Contracts\Fields\HasRelationshipContract;
use Leeto\MoonShine\Contracts\RenderableContract;
use Leeto\MoonShine\Traits\Fields\FormElementTrait;
use Leeto\MoonShine\Traits\Fields\ShowWhenTrait;
use Leeto\MoonShine\Traits\Fields\WithHtmlAttributes;
use Leeto\MoonShine\Traits\Fields\XModelTrait;

abstract class Filter implements RenderableContract
{
    use FormElementTrait, WithHtmlAttributes, ShowWhenTrait, XModelTrait;

    public function name(string $index = null): string
    {
        return $this->prepareName($index, 'filters');
    }

    public function getQuery(Builder $query): Builder
    {
        if($this instanceof HasRelationshipContract && !$this->isRelationToOne()) {
            $table = $query->getModel()->{$this->relation()}()->getRelated()->getTable();

            return $this->requestValue()
                ? $query->whereHas($this->relation(), function (Builder $q) use($table) {
                    return $q->whereIn("$table.id", $this->requestValue());
                })
                : $query;
        }

        return $this->requestValue()
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