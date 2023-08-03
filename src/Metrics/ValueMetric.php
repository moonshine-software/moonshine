<?php

declare(strict_types=1);

namespace MoonShine\Metrics;

use Closure;

class ValueMetric extends Metric
{
    protected string $view = 'moonshine::metrics.value';

    public int|float $value = 0;
    public int|float $target = 0;
    protected string $valueFormat = '{value}';
    protected bool $progress = false;

    public function valueFormat(string|Closure $value): static
    {
        $this->valueFormat = is_callable($value) ? $value() : $value;

        return $this;
    }

    public function valueResult(): string|float
    {
        if ($this->isProgress()) {
            return round(($this->value / $this->target) * 100);
        }

        return $this->simpleValue();
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

    public function value(int|float|Closure $value): static
    {
        $this->value = is_callable($value) ? $value() : $value;

        return $this;
    }

    public function progress(int|float|Closure $target): static
    {
        $this->progress = true;
        $this->target = is_callable($target) ? $target() : $target;

        return $this;
    }
}
