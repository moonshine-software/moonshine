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

    public function valueFormat(string|Closure $value): static
    {
        $this->valueFormat = value($value, $this->value);

        return $this;
    }

    public function valueResult(): string|float|int
    {
        if ($this->isProgress()) {
            return $this->progressValueResult();
        }

        return $this->simpleValue();
    }

    protected function progressValueResult(): float|int
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

    public function simpleValue(): string|float
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

        return $this;
    }

    public function progress(int|float|Closure $target): static
    {
        if (is_string($this->value)) {
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
            'valueResult' => $this->valueResult(),
            'simpleValue' => $this->simpleValue(),
        ];
    }
}
