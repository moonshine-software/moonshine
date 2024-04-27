<?php

declare(strict_types=1);

namespace MoonShine\Components\Metrics\Wrapped;

use Closure;
use MoonShine\AssetManager\Js;

class DonutChartMetric extends Metric
{
    protected string $view = 'moonshine::components.metrics.wrapped.donut-chart';

    protected array $values = [];

    public function getAssets(): array
    {
        return [
            Js::make('vendor/moonshine/libs/apexcharts/apexcharts.min.js'),
            Js::make('vendor/moonshine/libs/apexcharts/apexcharts-config.js'),
        ];
    }

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

    public function labels(): array
    {
        return array_keys($this->values);
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            'labels' => $this->labels(),
            'values' => $this->getValues(),
        ];
    }
}
