<?php

declare(strict_types=1);

namespace MoonShine\Components\Layout;

class TopBar extends WithComponents
{
    protected string $view = 'moonshine::components.layout.top-bar';

    protected array $actions = [];

    protected bool $hideLogo = false;

    protected bool $hideSwitcher = false;

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
