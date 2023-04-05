<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Metrics;

class DonutChartMetric extends Metric
{
    protected static string $view = 'moonshine::metrics.donut-chart';

    protected array $values = [];

    protected array $assets = [
        'vendor/moonshine/libs/apexcharts/apexcharts.min.js',
        'vendor/moonshine/libs/apexcharts/apexcharts-config.js',
    ];

    /**
     * @param $values array<string, int|float>
     */
    public function values(array $values): self
    {
        $this->values = $values;

        return $this;
    }

    /**
     * @return array<string, int|float>
     */
    public function getValues(): array
    {
        return array_values($this->values);
    }

    public function labels(): array
    {
        return array_keys($this->values);
    }
}
