<?php

declare(strict_types=1);

namespace MoonShine\Components;

use MoonShine\ActionButtons\ActionButtons;
use MoonShine\Fields\Fields;
use MoonShine\Traits\HasDataCast;
use Throwable;

abstract class IterableComponent extends MoonshineComponent
{
    use HasDataCast;

    protected array $fields = [];

    protected iterable $items = [];

    protected array $buttons = [];

    public function fields(array $fields): static
    {
        $this->fields = $fields;

        return $this;
    }

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

    /**
     * @throws Throwable
     */
    public function getFields(): Fields
    {
        return Fields::make($this->fields);
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