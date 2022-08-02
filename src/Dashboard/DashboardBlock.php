<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Dashboard;

use Illuminate\Contracts\View\View;
use Leeto\MoonShine\Contracts\Renderable;
use Leeto\MoonShine\Traits\Makeable;

class DashboardBlock implements Renderable, \Stringable, \JsonSerializable
{
    use Makeable;

    protected array $items = [];

    final public function __construct(array $items = [])
    {
        $this->setItems($items);
    }

    /**
     * @return array<Renderable>
     */
    public function items(): array
    {
        return $this->items;
    }

    /**
     * @param  array<Renderable>  $items
     */
    public function setItems(array $items): void
    {
        $this->items = $items;
    }

    public function render(): string|View
    {
        $renderer = '';

        foreach ($this->items() as $item) {
            $renderer .= $item;
        }

        return $renderer;
    }

    public function __toString()
    {
        return (string) $this->render();
    }

    public function jsonSerialize()
    {
        return [
            'dashboard-block' => $this,
        ];
    }
}
