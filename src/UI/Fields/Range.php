<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use MoonShine\UI\Contracts\Fields\DefaultValueTypes\DefaultCanBeArray;
use MoonShine\UI\Contracts\Fields\HasDefaultValue;
use MoonShine\UI\Contracts\Fields\RangeField;
use MoonShine\UI\Traits\Fields\NumberTrait;
use MoonShine\UI\Traits\Fields\RangeTrait;
use MoonShine\UI\Traits\Fields\WithDefaultValue;

class Range extends Field implements HasDefaultValue, DefaultCanBeArray, RangeField
{
    use RangeTrait;
    use NumberTrait;
    use WithDefaultValue;

    protected string $type = 'number';

    protected string $view = 'moonshine::fields.range';

    protected bool $isGroup = true;

    protected array $propertyAttributes = [
        'type',
        'min',
        'max',
        'step',
    ];

    protected function viewData(): array
    {
        return [
            'fromField' => $this->fromField,
            'toField' => $this->toField,
            'min' => $this->min,
            'max' => $this->max,
            'fromColumn' => "range_from_{$this->getIdentity()}",
            'toColumn' => "range_to_{$this->getIdentity()}",
            'fromValue' => data_get($this->getValue(), $this->fromField, $this->min),
            'toValue' => data_get($this->getValue(), $this->toField, $this->max),
            'fromAttributes' => $this->getFromAttributes(),
            'toAttributes' => $this->getToAttributes(),
        ];
    }
}
