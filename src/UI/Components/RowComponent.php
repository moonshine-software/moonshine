<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use MoonShine\Core\Contracts\MoonShineDataCast;
use MoonShine\UI\Collections\ActionButtons;
use MoonShine\UI\Contracts\Collections\FieldsCollection;
use MoonShine\UI\Contracts\Fields\HasFields;
use MoonShine\UI\Traits\HasDataCast;
use MoonShine\UI\Traits\WithFields;

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

    public function preparedFields(): FieldsCollection
    {
        $fields = $this->getFields();
        $casted = $this->castData($this->getValues());

        $fields->fill(
            $casted->toArray(),
            $casted
        );

        $fields->prepareAttributes();

        return $fields;
    }

    public function getButtons(): ActionButtons
    {
        return ActionButtons::make($this->buttons)
            ->fill($this->castData($this->getValues()))
            ->onlyVisible()
            ->withoutBulk();
    }
}
