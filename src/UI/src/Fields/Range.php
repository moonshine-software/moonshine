<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use MoonShine\UI\Contracts\DefaultValueTypes\CanBeArray;
use MoonShine\UI\Contracts\HasDefaultValueContract;
use MoonShine\UI\Contracts\RangeFieldContract;
use MoonShine\UI\Traits\Fields\NumberTrait;
use MoonShine\UI\Traits\Fields\RangeTrait;
use MoonShine\UI\Traits\Fields\WithDefaultValue;

class Range extends Field implements HasDefaultValueContract, CanBeArray, RangeFieldContract
{
    use RangeTrait;
    use NumberTrait;
    use WithDefaultValue;

    protected string $type = 'number';

    protected string $view = 'moonshine::fields.range';

    protected bool $isGroup = true;

    /**
     * @var string[]
     */
    protected array $propertyAttributes = [
        'type',
        'min',
        'max',
        'step',
    ];

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        $min = data_get($this->getValue(), $this->getFromField(), $this->min);
        $max = data_get($this->getValue(), $this->getToField(), $this->max);

        if (static::class !== self::class || ! $this->isNullable()) {
            $min ??= $this->min ?? 0;
            $max ??= $this->max ?? $this->step;
        }

        return [
            'fromField' => $this->getFromField(),
            'toField' => $this->getToField(),
            'min' => $this->min,
            'max' => $this->max,
            'fromColumn' => "range_from_{$this->getIdentity()}",
            'toColumn' => "range_to_{$this->getIdentity()}",
            'fromValue' => $min,
            'toValue' => $max,
            'fromAttributes' => $this->getFromAttributes(),
            'toAttributes' => $this->getToAttributes(),
        ];
    }
}
