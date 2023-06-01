<?php

declare(strict_types=1);

namespace MoonShine\Dashboard;

use Illuminate\Contracts\View\View;
use MoonShine\Contracts\ResourceRenderable;
use MoonShine\Traits\Makeable;
use MoonShine\Traits\WithColumnSpan;
use MoonShine\Traits\WithLabel;

/**
 * @method static static make(array $items = [], string $label = '')
 */
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

    public function render(ResourceRenderable $component): View
    {
        return view($component->getView(), [
            'block' => $this,
            'element' => $component,
        ]);
    }
}
