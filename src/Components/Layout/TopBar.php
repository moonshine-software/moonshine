<?php

declare(strict_types=1);

namespace MoonShine\Components\Layout;

class TopBar extends WithComponents
{
    protected string $view = 'moonshine::components.layout.top-bar';

    protected array $actions = [];

    public function actions(array $actions): self
    {
        $this->actions = $actions;

        return $this;
    }

    protected function viewData(): array
    {
        return parent::viewData() + ['_actions' => $this->actions];
    }
}
