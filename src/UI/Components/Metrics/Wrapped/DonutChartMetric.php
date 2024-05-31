<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Metrics\Wrapped;

use Closure;
use MoonShine\AssetManager\Js;

class DonutChartMetric extends Metric
{
    protected string $view = 'moonshine::components.metrics.wrapped.donut-chart';

    protected array $values = [];

    protected array $colors = [];

    public function getAssets(): array
    {
        return [
            Js::make('vendor/moonshine/libs/apexcharts/apexcharts.min.js'),
            Js::make('vendor/moonshine/libs/apexcharts/apexcharts-config.js'),
        ];
    }

    /**
     * @param array<string, int|float>|Closure $values
     */
    public function values(array|Closure $values): self
    {
        $this->values = $values instanceof Closure
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

    public function labels(): array
    {
        return array_keys($this->values);
    }

    /**
     * @return string[]
     */
    public function getColors(): array
    {
        return $this->colors;
    }

    /**
     * @param string[]|Closure $colors
     */
    public function colors(array|Closure $colors): self
    {
        $this->colors = $colors instanceof Closure
            ? $colors()
            : $colors;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            'labels' => $this->labels(),
            'values' => $this->getValues(),
            'colors' => $this->getColors(),
        ];
    }
}
