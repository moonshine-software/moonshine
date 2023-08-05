<?php

namespace MoonShine\Traits;

use MoonShine\ActionButtons\ActionButtons;

trait ComponentButtons
{
    protected array $buttons = [];

    public function buttons(array $buttons = []): self
    {
        $this->buttons = $buttons;

        return $this;
    }

    public function getButtons(): ActionButtons
    {
        $casted = $this->getCastedData();

        return ActionButtons::make($this->buttons)
            ->onlyVisible($casted)
            ->fillItem($casted);
    }
}