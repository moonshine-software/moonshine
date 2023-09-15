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

    protected function preparedFields(): Fields
    {
        $this->getFields()->fill(
            $this->getValues(),
            $this->castData($this->values)
        );

        $this->getFields()->prepareAttributes();

        return $this->getFields();
    }

    public function getButtons(): ActionButtons
    {
        $casted = $this->castData($this->values);

        return ActionButtons::make($this->buttons)
            ->onlyVisible($casted)
            ->fillItem($casted);
    }
}
