<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Metrics;

class LineChartMetric extends Metric
{
    protected static string $component = 'LineChart';

    protected array $lines = [];

    protected array $colors = [];

    public function lines(): array
    {
        return $this->lines;
    }

    public function line(array $line, string $color = '#7843E9'): static
    {
        $this->lines[] = $line;
        $this->colors[] = $color;

        return $this;
    }

    public function color(int $index): string
    {
        return $this->colors[$index];
    }

    public function labels(): array
    {
        return collect($this->lines())
            ->collapse()
            ->mapWithKeys(fn($item) => $item)
            ->sortKeys()
            ->keys()
            ->toArray();
    }

    public function jsonSerialize(): array
    {
        return [
            'component' => $this->getComponent(),
            'id' => $this->id(),
            'name' => $this->name(),
            'label' => $this->label(),
            'labels' => $this->labels(),
            'lines' => $this->lines()
        ];
    }
}
