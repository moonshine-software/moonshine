<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use MoonShine\Core\Contracts\Fields\DefaultValueTypes\DefaultCanBeArray;
use MoonShine\Core\Contracts\Fields\RangeField;
use MoonShine\UI\Traits\Fields\RangeTrait;

class Range extends Number implements DefaultCanBeArray, RangeField
{
    use RangeTrait;

    protected string $view = 'moonshine::fields.range';

    protected bool $isGroup = true;

    protected function viewData(): array
    {
        return [
            'fromField' => $this->fromField,
            'toField' => $this->toField,
            'min' => $this->min,
            'max' => $this->max,
            'fromColumn' => "range_from_{$this->identity()}",
            'toColumn' => "range_to_{$this->identity()}",
            'fromValue' => data_get($this->value(), $this->fromField, $this->min),
            'toValue' => data_get($this->value(), $this->toField, $this->max),
            'fromAttributes' => $this->getFromAttributes(),
            'toAttributes' => $this->getToAttributes(),
        ];
    }
}
