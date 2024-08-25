<?php


declare(strict_types=1);

namespace MoonShine\UI\Components\Layout;

use MoonShine\Contracts\MenuManager\MenuElementsContract;
use MoonShine\Contracts\MenuManager\MenuManagerContract;
use MoonShine\UI\Components\MoonShineComponent;

/**
 * @method static static make(MenuManagerContract $menuManager)
 */
class Menu extends MoonShineComponent
{
    protected string $view = 'moonshine::components.menu.index';

    public MenuElementsContract $items;

    public function __construct(
        private readonly MenuManagerContract $menuManager,
        protected bool $top = false,
        protected bool $scrollTo = true,
    ) {
        parent::__construct();

        $this->items = $this->menuManager->all();
    }

    public function top(): static
    {
        $this->top = true;

        return $this;
    }

    public function isTop(): bool
    {
        return $this->top;
    }

    public function withoutScrollTo(): static
    {
        $this->scrollTo = false;

        return $this;
    }

    public function scrollTo(): static
    {
        $this->scrollTo = true;

        return $this;
    }

    public function isScrollTo(): bool
    {
        return $this->scrollTo;
    }

    protected function prepareBeforeRender(): void
    {
        parent::prepareBeforeRender();

        if(! $this->isTop() && $this->isScrollTo()) {
            $this->customAttributes([
                'x-init' => "\$nextTick(() => \$el.querySelector('.menu-inner-item._is-active')?.scrollIntoView())",
            ]);
        }

        if($this->isTop()) {
            $this->items->topMode();
        }
    }
}
