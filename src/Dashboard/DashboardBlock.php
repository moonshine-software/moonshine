<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Dashboard;

use JsonSerializable;
use Leeto\MoonShine\Metrics\Metric;
use Leeto\MoonShine\Traits\Makeable;

final class DashboardBlock implements JsonSerializable
{
    use Makeable;

    final public function __construct(protected array $items = [])
    {
    }

    /**
     * @return array<Metric>
     */
    public function items(): array
    {
        return $this->items;
    }

    public function jsonSerialize(): array
    {
        return [
            'blocks' => $this->items(),
        ];
    }
}
