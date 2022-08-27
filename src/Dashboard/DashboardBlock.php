<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Dashboard;

use JsonSerializable;
use Leeto\MoonShine\Metrics\Metric;
use Leeto\MoonShine\Traits\Makeable;

final class DashboardBlock implements JsonSerializable
{
    use Makeable;

    protected array $items = [];

    final public function __construct(array $items = [])
    {
        $this->setItems($items);
    }

    /**
     * @return array<Metric>
     */
    public function items(): array
    {
        return $this->items;
    }

    /**
     * @param  array<Metric>  $items
     */
    public function setItems(array $items): void
    {
        $this->items = $items;
    }

    public function jsonSerialize(): array
    {
        return [
            'blocks' => $this->items(),
        ];
    }
}
