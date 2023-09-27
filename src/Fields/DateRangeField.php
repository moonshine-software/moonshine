<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Support\Carbon;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeArray;
use MoonShine\Contracts\Fields\HasDefaultValue;
use MoonShine\Traits\Fields\DateTrait;
use MoonShine\Traits\Fields\RangeTrait;
use MoonShine\Traits\Fields\WithDefaultValue;

class DateRangeField extends Field implements HasDefaultValue, DefaultCanBeArray
{
    use RangeTrait;
    use DateTrait;
    use WithDefaultValue;

    protected string $type = 'date';

    protected string $view = 'moonshine::fields.range';

    protected bool $isGroup = true;

    protected array $attributes = [
        'type',
        'min',
        'max',
        'step',
        'disabled',
        'readonly',
        'required',
    ];
    public string $min = '';

    public string $max = '';

    public int|float|string $step = 'any';

    public function min(string $min): static
    {
        $this->min = $min;

        return $this;
    }

    public function max(string $max): static
    {
        $this->max = $max;

        return $this;
    }

    public function step(int|float|string $step): static
    {
        $this->step = $step;

        return $this;
    }

    private function extractDates(array $value, string $format): array
    {
        return [
            $this->fromField => Carbon::parse($value[$this->fromField])->format($format),
            $this->toField => Carbon::parse($value[$this->toField])->format($format),
        ];
    }

    private function isNullDates(): bool
    {
        if (is_array($this->toValue())) {
            return array_filter($this->toValue()) === [];
        }

        return true;
    }

    protected function resolveValue(): mixed
    {
        if ($this->isNullDates()) {
            return [
                $this->fromField => null,
                $this->toField => null,
            ];
        }

        return $this->extractDates($this->toValue(), $this->getInputFormat());
    }

    protected function resolvePreview(): string
    {
        $value = $this->toFormattedValue();

        if ($this->isNullDates()) {
            return '';
        }

        if ($this->isRawMode()) {
            $value = $this->toValue(withDefault: false);

            return "{$value[$this->fromField]} - {$value[$this->toField]}";
        }

        $dates = $this->extractDates($value, $this->getFormat());

        return "{$dates[$this->fromField]} - {$dates[$this->toField]}";
    }
}
