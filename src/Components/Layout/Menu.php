<?php


declare(strict_types=1);

namespace MoonShine\Components\Layout;

use MoonShine\Components\MoonShineComponent;

class Menu extends MoonShineComponent
{
    protected bool $top = false;

    protected bool $scrollTo = true;

    protected string $view = 'moonshine::components.menu.index';

    public function __construct(protected ?array $items = null)
    {
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

    protected function viewData(): array
    {
        return [
            '_data' => is_null($this->items)
                ? moonshineMenu()->all()
                : moonshineMenu()->prepareMenu($this->items),
            'isTop' => $this->isTop(),
            'isScrollTo' => $this->isScrollTo(),
        ];
    }
}
