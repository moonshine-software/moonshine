<?php


declare(strict_types=1);

namespace MoonShine\Components\Layout;

use MoonShine\Components\MoonShineComponent;

class Menu extends MoonShineComponent
{
    protected bool $top = false;

    protected bool $scrollTo = true;

    protected string $view = 'moonshine::components.menu.index';

    public function __construct(public ?iterable $items = null)
    {
        parent::__construct();

        $this->items = moonshineMenu()->all($this->items);
    }

    public function top(): self
    {
        $this->top = true;

        return $this;
    }

    public function isTop(): bool
    {
        return $this->top;
    }

    public function withoutScrollTo(): self
    {
        $this->scrollTo = false;

        return $this;
    }

    public function scrollTo(): self
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
