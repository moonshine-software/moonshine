<?php


declare(strict_types=1);

namespace MoonShine\UI\Components\Layout;

use MoonShine\Contracts\MenuManager\MenuElementContract;
use MoonShine\Contracts\MenuManager\MenuElementsContract;
use MoonShine\Contracts\MenuManager\MenuManagerContract;
use MoonShine\UI\Components\MoonShineComponent;

/**
 * @method static static make(?iterable $elements = null, bool $top = false, bool $scrollTo = false)
 */
class Menu extends MoonShineComponent
{
    protected string $view = 'moonshine::components.menu.index';

    public MenuElementsContract $items;

    /**
     * @param  iterable<array-key, MenuElementContract>|null  $elements
     */
    public function __construct(
        private readonly ?iterable $elements = null,
        protected bool $top = false,
        protected bool $scrollTo = false,
    ) {
        parent::__construct();

        $this->items = $this->getCore()
            ->getContainer(MenuManagerContract::class)
            ->all($this->elements);
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

        if (! $this->isTop() && $this->isScrollTo()) {
            $this->customAttributes([
                'x-init' => "\$nextTick(() => \$el.querySelector('.menu-item._is-active')?.scrollIntoView())",
            ]);
        }

        if ($this->isTop()) {
            $this->items->topMode();
        }
    }
}
