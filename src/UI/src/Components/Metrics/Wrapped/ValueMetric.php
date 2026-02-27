<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Metrics\Wrapped;

use Closure;

class ValueMetric extends Metric
{
    protected string $view = 'moonshine::components.metrics.wrapped.value';

    public int|string|float $value = 0;

    public int|float $target = 0;

    protected string $valueFormat = '{value}';

    protected bool $progress = false;

    protected bool $valueRaw = false;

    public function valueFormat(string|Closure $value): static
    {
        $this->valueFormat = value($value, $this->value);
        $this->valueRaw = false;

        return $this;
    }

    public function valueFormatHtml(string|Closure $value): static
    {
        $this->valueFormat = value($value, $this->value);
        $this->valueRaw = true;

        return $this;
    }

    public function getValueResult(): string|float|int
    {
        if ($this->isProgress()) {
            return $this->getProgressValueResult();
        }

        return $this->getSimpleValue();
    }

    protected function getProgressValueResult(): float|int
    {
        if ($this->target <= 0 || $this->value <= 0) {
            return $this->value;
        }

        return round(($this->value / $this->target) * 100);
    }

    public function isProgress(): bool
    {
        return $this->progress;
    }

    public function getSimpleValue(): string|float
    {
        return str_replace(
            '{value}',
            (string) $this->value,
            $this->valueFormat
        );
    }

    public function value(int|string|float|Closure $value): static
    {
        $this->value = value($value);
        $this->valueRaw = false;

        return $this;
    }

    public function valueHtml(int|string|float|Closure $value): static
    {
        $this->value = value($value);
        $this->valueRaw = true;

        return $this;
    }

    public function isValueRaw(): bool
    {
        return $this->valueRaw;
    }

    public function progress(int|float|Closure $target): static
    {
        if (\is_string($this->value)) {
            return $this;
        }

        $this->progress = true;
        $this->target = value($target, $this->value);

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            'isProgress' => $this->isProgress(),
            'valueResult' => $this->getValueResult(),
            'simpleValue' => $this->getSimpleValue(),
            'valueRaw' => $this->isValueRaw(),
        ];
    }
}
