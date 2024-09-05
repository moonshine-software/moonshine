<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Metrics\Wrapped;

use Closure;
use Illuminate\Support\Collection;
use MoonShine\AssetManager\Js;

class LineChartMetric extends Metric
{
    protected string $view = 'moonshine::components.metrics.wrapped.line-chart';

    protected array $lines = [];

    protected array $colors = [];

    protected bool $withoutSortKeys = false;

    public function getAssets(): array
    {
        return [
            Js::make('vendor/moonshine/libs/apexcharts/apexcharts.min.js'),
            Js::make('vendor/moonshine/libs/apexcharts/apexcharts-config.js'),
        ];
    }

    /**
     * @param  array<string, array<numeric>>|Closure  $line
     * @param  string|string[]|Closure  $color
     *
     * @return $this
     */
    public function line(
        array|Closure $line,
        string|array|Closure $color = '#7843E9'
    ): static {
        $this->lines[] = $line instanceof Closure ? $line() : $line;

        $color = $color instanceof Closure ? $color() : $color;

        if (is_string($color)) {
            $this->colors[] = $color;
        } else {
            $this->colors = $color;
        }

        return $this;
    }

    public function getColor(int $index): string
    {
        return $this->colors[$index];
    }

    public function getColors(): array
    {
        return $this->colors;
    }

    public function getLabels(): array
    {
        return collect($this->getLines())
            ->collapse()
            ->mapWithKeys(static fn ($item): mixed => $item)
            ->when(! $this->isWithoutSortKeys(), static fn ($items): Collection => $items->sortKeys())
            ->keys()
            ->toArray();
    }

    public function getLines(): array
    {
        return $this->lines;
    }

    public function withoutSortKeys(): static
    {
        $this->withoutSortKeys = true;

        return $this;
    }

    public function isWithoutSortKeys(): bool
    {
        return $this->withoutSortKeys;
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            'labels' => $this->getLabels(),
            'lines' => $this->getLines(),
            'colors' => $this->getColors(),
        ];
    }
}
