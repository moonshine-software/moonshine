<?php

declare(strict_types=1);

namespace MoonShine\Components;

use MoonShine\ActionButtons\ActionButtons;
use MoonShine\Fields\Fields;
use MoonShine\Traits\HasDataCast;
use MoonShine\Traits\WithFields;

abstract class RowComponent extends MoonshineComponent
{
    use HasDataCast;
    use WithFields;

    protected array $values = [];

    protected array $buttons = [];

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

    protected function prepareFields(Fields $fields): Fields
    {
        $fields->fill(
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
