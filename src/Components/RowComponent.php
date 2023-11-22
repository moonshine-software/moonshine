<?php

declare(strict_types=1);

namespace MoonShine\Components;

use MoonShine\ActionButtons\ActionButtons;
use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Contracts\MoonShineDataCast;
use MoonShine\Fields\Fields;
use MoonShine\Traits\HasDataCast;
use MoonShine\Traits\WithFields;

abstract class RowComponent extends MoonShineComponent implements HasFields
{
    use HasDataCast;
    use WithFields;

    protected mixed $values = [];

    protected array $buttons = [];

    public function fill(mixed $values = []): static
    {
        $this->values = $values;

        return $this;
    }

    public function fillCast(mixed $values, MoonShineDataCast $cast): static
    {
        return $this
            ->cast($cast)
            ->fill($values);
    }

    public function getValues(): mixed
    {
        return $this->values ?? [];
    }

    public function buttons(array $buttons = []): static
    {
        $this->buttons = $buttons;

        return $this;
    }

    public function preparedFields(): Fields
    {
        $fields = $this->getFields();

        $fields->fill(
            $this->unCastData($this->getValues()),
            $this->castData($this->getValues())
        );

        $fields->prepareAttributes();

        return $fields;
    }

    public function getButtons(): ActionButtons
    {
        return ActionButtons::make($this->buttons)
            ->filter()
            ->fillItem($this->castData($this->getValues()))
            ->onlyVisible()
            ->withoutBulk();
    }
}
