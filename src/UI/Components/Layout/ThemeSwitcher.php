<?php


declare(strict_types=1);

namespace MoonShine\UI\Components\Layout;

use MoonShine\UI\Components\MoonShineComponent;

class ThemeSwitcher extends MoonShineComponent
{
    protected string $view = 'moonshine::components.layout.theme-switcher';

    public function __construct(protected bool $top = false)
    {
        parent::__construct();
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

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            'top' => $this->isTop(),
        ];
    }
}
