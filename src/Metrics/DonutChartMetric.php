<?php

declare(strict_types=1);

namespace MoonShine\Metrics;

use Closure;

class DonutChartMetric extends Metric
{
    protected string $view = 'moonshine::metrics.donut-chart';

    protected array $values = [];

    protected array $colors = [];

    protected int $decimal = 3;

    protected array $assets = [
        'vendor/moonshine/libs/apexcharts/apexcharts.min.js',
        'vendor/moonshine/libs/apexcharts/apexcharts-config.js',
    ];

    /**
     * @param $values array<string, int|float>|Closure
     */
    public function values(array|Closure $values): self
    {
        $this->values = is_closure($values)
            ? $values()
            : $values;

        return $this;
    }

    /**
     * @return array<string, int|float>
     */
    public function getValues(): array
    {
        return array_values($this->values);
    }

    /**
     * @return string[]
     */
    public function getColors(): array
    {
        return $this->colors;
    }

    /**
     * @param $values string[]|Closure
     */
    public function colors(array|Closure $colors): self
    {
        $this->colors = is_closure($colors)
            ? $colors()
            : $colors;

        return $this;
    }

    /**
     * @return int
     */
    public function getDecimal(): int
    {
        return $this->decimal;
    }

    /**
     * @param int $decimal
     */
    public function decimal(int $decimal): self
    {
        if (in_array($decimal, range(0, 100))) {
            $this->decimal = $decimal;
        }

        return $this;
    }

    public function labels(): array
    {
        return array_keys($this->values);
    }
}
