<?php

declare(strict_types=1);

namespace MoonShine\Crud\Contracts;

use Closure;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Crud\Components\Fragment;
use MoonShine\UI\Components\Metrics\Wrapped\Metric;

interface HasMetricsContract
{
    /**
     * @return list<Metric>
     */
    public function getMetrics(): array;

    /**
     * @return null|Closure(list<ComponentContract> $components): Fragment
     */
    public function getFragmentMetrics(): ?Closure;
}
