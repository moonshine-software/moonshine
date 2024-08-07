<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Illuminate\Support\Carbon;
use MoonShine\UI\Contracts\DefaultValueTypes\CanBeArray;
use MoonShine\UI\Contracts\HasDefaultValueContract;
use MoonShine\UI\Contracts\RangeFieldContract;
use MoonShine\UI\Traits\Fields\DateTrait;
use MoonShine\UI\Traits\Fields\RangeTrait;
use MoonShine\UI\Traits\Fields\WithDefaultValue;

class DateRange extends Field implements HasDefaultValueContract, CanBeArray, RangeFieldContract
{
    use RangeTrait;
    use DateTrait;
    use WithDefaultValue;

    protected string $type = 'date';

    protected string $view = 'moonshine::fields.range';

    protected bool $isGroup = true;

    protected array $propertyAttributes = [
        'type',
        'min',
        'max',
        'step',
    ];

    public string $min = '';

    public string $max = '';

    public int|float|string $step = 'any';

    public function min(string $min): static
    {
        $this->min = $min;
        $this->getAttributes()->set('min', $this->min);

        return $this;
    }

    public function max(string $max): static
    {
        $this->max = $max;
        $this->getAttributes()->set('max', $this->max);

        return $this;
    }

    public function step(int|float|string $step): static
    {
        $this->step = $step;
        $this->getAttributes()->set('step', $this->step);

        return $this;
    }

    private function extractDates(array $value, string $format): array
    {
        return [
            $this->fromField => isset($value[$this->fromField])
                ? Carbon::parse($value[$this->fromField])->format($format)
                : '',
            $this->toField => isset($value[$this->toField])
                ? Carbon::parse($value[$this->toField])->format($format)
                : '',
        ];
    }

    protected function resolveValue(): mixed
    {
        if ($this->isNullRange()) {
            return [
                $this->fromField => null,
                $this->toField => null,
            ];
        }

        return $this->extractDates($this->toValue(), $this->getInputFormat());
    }

    protected function resolveRawValue(): mixed
    {
        if ($this->isNullRange(formatted: true)) {
            return '';
        }

        $value = $this->toValue(withDefault: false);

        return "{$value[$this->fromField]} - {$value[$this->toField]}";
    }

    protected function resolvePreview(): string
    {
        $value = $this->toFormattedValue();

        if ($this->isNullRange(formatted: true)) {
            return '';
        }

        $dates = $this->extractDates($value, $this->getFormat());

        return "{$dates[$this->fromField]} - {$dates[$this->toField]}";
    }

    protected function viewData(): array
    {
        return [
            'fromField' => $this->fromField,
            'toField' => $this->toField,
            'min' => $this->min,
            'max' => $this->max,
            'fromColumn' => "date_range_from_{$this->getIdentity()}",
            'toColumn' => "date_range_to_{$this->getIdentity()}",
            'fromValue' => data_get($this->getValue(), $this->fromField, $this->min),
            'toValue' => data_get($this->getValue(), $this->toField, $this->max),
            'fromAttributes' => $this->getFromAttributes(),
            'toAttributes' => $this->getToAttributes(),
        ];
    }
}
