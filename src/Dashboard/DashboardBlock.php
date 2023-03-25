<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Dashboard;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Leeto\MoonShine\Contracts\ResourceRenderable;
use Leeto\MoonShine\Traits\Makeable;
use Leeto\MoonShine\Traits\WithColumnSpan;
use Leeto\MoonShine\Traits\WithLabel;

class DashboardBlock
{
    use Makeable;
    use WithColumnSpan;
    use WithLabel;

    protected array $items = [];

    final public function __construct(array $items = [], string $label = '')
    {
        $this->setItems($items);
        $this->setLabel($label);
    }

    /**
     * @return array
     */
    public function items(): array
    {
        return $this->items;
    }

    /**
     * @param  array  $items
     */
    public function setItems(array $items): void
    {
        $this->items = $items;
    }

    public function render(ResourceRenderable $item): Factory|View|Application
    {
        return view($item->getView(), [
            'block' => $this,
            'item' => $item,
        ]);
    }
}
