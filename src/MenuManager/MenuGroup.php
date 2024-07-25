<?php

declare(strict_types=1);

namespace MoonShine\MenuManager;

use Closure;
use MoonShine\Contracts\MenuManager\MenuElementsContract;

/**
 * @method static static make(Closure|string $label, iterable $items, string|null $icon = null)
 */
class MenuGroup extends MenuElement
{
    protected string $view = 'moonshine::components.menu.group';

    public function __construct(
        Closure|string $label,
        protected iterable $items = [],
        string $icon = null,
    ) {
        parent::__construct();

        $this->setLabel($label);

        if ($icon) {
            $this->icon($icon);
        }
    }

    public function onFiller(Closure $onFiller): static
    {
        parent::onFiller($onFiller);

        foreach ($this->getItems() as $item) {
            $item->onFiller($onFiller);
        }

        return $this;
    }

    public function onRender(Closure $onRender): static
    {
        parent::onRender($onRender);

        foreach ($this->getItems() as $item) {
            $item->onRender($onRender);
        }

        return $this;
    }

    public function onIsActive(Closure $onIsActive): static
    {
        parent::onIsActive($onIsActive);

        foreach ($this->getItems() as $item) {
            $item->onIsActive($onIsActive);
        }

        return $this;
    }

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

    public function viewData(): array
    {
        return [
            'items' => $this->getItems(),
        ];
    }
}
