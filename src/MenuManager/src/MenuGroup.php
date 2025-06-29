<?php

declare(strict_types=1);

namespace MoonShine\MenuManager;

use Closure;
use MoonShine\Contracts\MenuManager\MenuElementContract;
use MoonShine\Contracts\MenuManager\MenuElementsContract;

/**
 * @method static static make(Closure|string $label, iterable $items, string|null $icon = null)
 */
class MenuGroup extends MenuElement
{
    protected string $view = 'moonshine::components.menu.group';

    /**
     * @param  iterable<array-key, MenuElementContract>  $items
     */
    public function __construct(
        Closure|string $label,
        protected iterable $items = [],
        ?string $icon = null,
    ) {
        parent::__construct();

        $this->setLabel($label);

        if ($icon) {
            $this->icon($icon);
        }
    }

    /**
     * @param  iterable<array-key, MenuElementContract>  $items
     *
     */
    public function setItems(iterable $items): static
    {
        $this->items = $items;

        return $this;
    }

    public function getItems(): MenuElementsContract
    {
        return MenuElements::make($this->items);
    }

    public function isActive(): bool
    {
        foreach ($this->getItems() as $item) {
            if ($item->isActive()) {
                return true;
            }
        }

        return false;
    }

    protected function viewData(): array
    {
        return [
            'items' => $this->getItems(),
        ];
    }
}
