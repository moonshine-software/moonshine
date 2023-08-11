<?php

declare(strict_types=1);

namespace MoonShine\Components;

use MoonShine\ActionButtons\ActionButtons;
use MoonShine\Traits\HasDataCast;
use MoonShine\Traits\WithFields;

abstract class IterableComponent extends MoonshineComponent
{
    use HasDataCast;
    use WithFields;

    protected iterable $items = [];

    protected array $buttons = [];

    public function items(iterable $items = []): self
    {
        $this->items = $items;

        return $this;
    }

    public function buttons(array $buttons = []): static
    {
        $this->buttons = $buttons;

        return $this;
    }

    public function getButtons(array $data): ActionButtons
    {
        $casted = $this->castData($data);

        return ActionButtons::make($this->buttons)
            ->onlyVisible($casted)
            ->fillItem($casted)
            ->withoutBulk();
    }
}
