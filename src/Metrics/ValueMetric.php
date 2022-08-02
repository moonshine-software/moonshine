<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Metrics;

class ValueMetric extends Metric
{
    protected static string $view = 'value';

    public int|float $value = 0;

    protected string $valueFormat = '{value}';

    protected bool $progress = false;

    public int|float $target = 0;

    public function valueFormat(string $value): static
    {
        $this->valueFormat = $value;

        return $this;
    }

    public function valueResult(): string|float
    {
        if ($this->isProgress()) {
            return round(($this->value / $this->target) * 100);
        }

        return str_replace('{value}', (string) $this->value, $this->valueFormat);
    }

    public function value(int|float $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function progress(int|float $target): static
    {
        $this->progress = true;
        $this->target = $target;

        return $this;
    }

    public function isProgress(): bool
    {
        return $this->progress;
    }
}
