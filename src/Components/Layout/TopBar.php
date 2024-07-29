<?php

declare(strict_types=1);

namespace MoonShine\Components\Layout;

use MoonShine\Collections\MoonShineRenderElements;

class TopBar extends WithComponents
{
    protected string $view = 'moonshine::components.layout.top-bar';

    protected array $actions = [];

    public function __construct(
        array|MoonShineRenderElements $components = [],
        public bool $hideLogo = false,
        public bool $hideSwitcher = false
    ) {
        parent::__construct($components);
    }

    public function hideLogo(): self
    {
        $this->hideLogo = true;

        return $this;
    }

    public function hideSwitcher(): self
    {
        $this->hideSwitcher = true;

        return $this;
    }

    public function actions(array $actions): self
    {
        $this->actions = $actions;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            ...parent::viewData(),
            '_actions' => $this->actions,
            'hideLogo' => $this->hideLogo,
            'hideSwitcher' => $this->hideSwitcher,
        ];
    }
}
