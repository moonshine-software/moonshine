<?php

declare(strict_types=1);

namespace MoonShine\Metrics;

use Closure;

class ValueMetric extends Metric
{
    protected string $view = 'moonshine::metrics.value';

    public int|string|float $value = 0;

    public int|float $target = 0;

    protected string $valueFormat = '{value}';

    protected bool $progress = false;

    protected bool $short = false;

    public function valueFormat(string|Closure $value): static
    {
        $this->valueFormat = is_closure($value) ? $value() : $value;

        return $this;
    }

    public function valueResult(): string|float
    {
        if ($this->isProgress()) {
            return round(($this->value / $this->target) * 100);
        }

        if($this->isShort()) {
            return $this->shortValue();
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

    public function value(int|string|float|Closure $value): static
    {
        $this->value = is_closure($value) ? $value() : $value;

        return $this;
    }

    public function progress(int|float|Closure $target): static
    {
        if (is_string($this->value)) {
            return $this;
        }

        $this->progress = true;
        $this->target = is_closure($target) ? $target() : $target;

        return $this;
    }

    public function short(): static
    {
        $this->short = true;

        return $this;
    }

    public function isShort(): bool
    {
        return $this->short;
    }

    public function shortValue(): string|float
    {
        $abbreviations = ['T', 'B', 'M', 'K'];
        $len = strlen((string) $this->value);
        $rest = (int) substr((string) $this->value, 2, $len);
        $checkPlus = (is_int($rest) && !empty($rest)) ? "+" : "";

        foreach ([1000000000000, 1000000000, 1000000, 1000] as $index => $limit) {
            if ($this->value >= $limit) {
                $this->value = rtrim(rtrim(number_format($this->value / $limit, 1), '0'), '.');
                $this->valueFormat.= $abbreviations[$index];
                break;
            }
        }

        $this->valueFormat .= $checkPlus;
        return $this->simpleValue();
    }

}
