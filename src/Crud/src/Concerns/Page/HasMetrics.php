<?php

declare(strict_types=1);

namespace MoonShine\Crud\Concerns\Page;

use Closure;
use Illuminate\Support\Collection;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Crud\Components\Fragment;
use MoonShine\Crud\Contracts\Page\IndexPageContract;
use MoonShine\UI\Components\Metrics\Wrapped\Metric;

/**
 * @mixin IndexPageContract
 */
trait HasMetrics
{
    /**
     * @return list<Metric>
     */
    protected function metrics(): array
    {
        return [];
    }

    /**
     * @return list<Metric>
     */
    public function getMetrics(): array
    {
        return (new Collection($this->metrics()))
            ->ensure(Metric::class)
            ->toArray();
    }

    /**
     * @return null|Closure(list<ComponentContract> $components): Fragment
     */
    protected function fragmentMetrics(): ?Closure
    {
        return null;
    }

    /**
     * @return null|Closure(list<ComponentContract> $components): Fragment
     */
    public function getFragmentMetrics(): ?Closure
    {
        return $this->fragmentMetrics();
    }
}
