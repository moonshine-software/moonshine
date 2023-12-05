<?php

declare(strict_types=1);

namespace MoonShine\Components\Layout;

/**
 * @method static static make(array $components = [])
 */
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
        return parent::viewData() + ['actions' => $this->actions];
    }
}
