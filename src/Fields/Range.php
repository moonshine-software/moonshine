<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeArray;
use MoonShine\Contracts\Fields\HasDefaultValue;
use MoonShine\Contracts\Fields\RangeField;
use MoonShine\Traits\Fields\NumberTrait;
use MoonShine\Traits\Fields\RangeTrait;
use MoonShine\Traits\Fields\WithDefaultValue;

class Range extends Field implements HasDefaultValue, DefaultCanBeArray, RangeField
{
    use RangeTrait;
    use NumberTrait;
    use WithDefaultValue;

    protected string $view = 'moonshine::fields.range';

    protected string $type = 'number';

    protected array $attributes = [
        'type',
        'min',
        'max',
        'step',
        'disabled',
        'readonly',
        'required',
    ];

    protected bool $isGroup = true;
}
