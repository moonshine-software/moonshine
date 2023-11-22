<?php

declare(strict_types=1);

namespace MoonShine\Components\Layout;

use MoonShine\Components\MoonShineComponent;

class Menu extends MoonShineComponent
{
    protected bool $top = false;

    protected string $view = 'moonshine::components.menu.index';

    public function top(): self
    {
        $this->top = true;

        return $this;
    }

    public function isTop(): bool
    {
        return $this->top;
    }

    protected function viewData(): array
    {
        return [
            'data' => moonshineMenu()->all(),
            'top' => $this->isTop(),
        ];
    }
}
