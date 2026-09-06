<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use DateTimeInterface;
use Illuminate\Support\Carbon;
use MoonShine\Support\Stringify;
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

    /**
     * @var string[]
     */
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
        $this->setAttribute('min', $this->min);

        return $this;
    }

    public function max(string $max): static
    {
        $this->max = $max;
        $this->setAttribute('max', $this->max);

        return $this;
    }

    public function step(int|float|string $step): static
    {
        $this->step = $step;
        $this->setAttribute('step', (string) $this->step);

        return $this;
    }

    /**
     * @param  array<array-key, mixed>  $value
     * @return array<string, string>
     */
    private function extractDates(array $value, string $format): array
    {
        $from = $value[$this->getFromField()] ?? null;
        $to = $value[$this->getToField()] ?? null;

        return [
            $this->getFromField() => $from !== null
                ? Carbon::parse(\is_int($from) || $from instanceof DateTimeInterface ? $from : Stringify::value($from))->format($format)
                : '',
            $this->getToField() => $to !== null
                ? Carbon::parse(\is_int($to) || $to instanceof DateTimeInterface ? $to : Stringify::value($to))->format($format)
                : '',
        ];
    }

    /**
     * @return array<string, null|string>
     */
    protected function resolveValue(): mixed
    {
        if ($this->isNullRange()) {
            return [
                $this->getFromField() => null,
                $this->getToField() => null,
            ];
        }

        $value = $this->toValue();

        return $this->extractDates(\is_array($value) ? $value : [], $this->getInputFormat());
    }

    protected function resolveRawValue(): mixed
    {
        if ($this->isNullRange(formatted: true)) {
            return '';
        }

        $value = $this->toValue(withDefault: false);

        return \is_array($value)
            ? Stringify::value($value[$this->getFromField()] ?? '') . ' - ' . Stringify::value($value[$this->getToField()] ?? '')
            : '';
    }

    protected function resolvePreview(): string
    {
        $value = $this->toFormattedValue();

        if (! \is_array($value) || $this->isNullRange(formatted: true)) {
            return '';
        }

        $dates = $this->extractDates($value, $this->getFormat());

        return "{$dates[$this->getFromField()]} - {$dates[$this->getToField()]}";
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            'fromField' => $this->getFromField(),
            'toField' => $this->getToField(),
            'min' => $this->min,
            'max' => $this->max,
            'fromColumn' => "date_range_from_{$this->getIdentity()}",
            'toColumn' => "date_range_to_{$this->getIdentity()}",
            'fromValue' => data_get($this->getValue(), $this->getFromField(), $this->min),
            'toValue' => data_get($this->getValue(), $this->getToField(), $this->max),
            'fromAttributes' => $this->getFromAttributes(),
            'toAttributes' => $this->getToAttributes(),
        ];
    }
}
