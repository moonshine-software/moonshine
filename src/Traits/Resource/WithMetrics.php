<?php

declare(strict_types=1);

namespace MoonShine\Traits\Resource;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Metrics\Metric;

trait WithMetrics
{
    /**
     * Get an array of metrics which will be displayed on resource index page
     *
     * @return Metric[]
     */
    public function metrics(): array
    {
        return [];
    }

    /**
     * Get an array of metrics which will be displayed on resource create page
     *
     * @return Metric[]
     */
    public function metricsOnCreate(): array
    {
        return [];
    }

    /**
     * Get an array of metrics which will be displayed on resource edit page
     *
     * @return Metric[]
     */
    public function metricsOnEdit(Model $item): array
    {
        return [];
    }

    /**
     * Get an array of metrics which will be displayed on resource show page
     *
     * @return Metric[]
     */
    public function metricsOnShow(Model $item): array
    {
        return [];
    }
}
