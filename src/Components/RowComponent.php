<?php

declare(strict_types=1);

namespace MoonShine\Components;

use MoonShine\ActionButtons\ActionButtons;
use MoonShine\Fields\Fields;
use MoonShine\Traits\HasDataCast;
use Throwable;

abstract class RowComponent extends MoonshineComponent
{
    use HasDataCast;

    protected array $fields = [];

    protected array $values = [];

    protected array $buttons = [];

    public function fields(array $fields): static
    {
        $this->fields = $fields;

        return $this;
    }

    public function fill(array $values = []): static
    {
        $this->values = $values;

        return $this;
    }

    public function getValues(): array
    {
        return $this->values;
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
        $fields = Fields::make($this->fields);
        $fields->fillValues(
            $this->getValues(),
            $this->castData($this->values)
        );

        return $fields;
    }

    public function getButtons(): ActionButtons
    {
        $casted = $this->castData($this->values);

        return ActionButtons::make($this->buttons)
            ->onlyVisible($casted)
            ->fillItem($casted);
    }
}