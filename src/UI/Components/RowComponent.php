<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\TypeCasts\DataCasterContract;
use MoonShine\Contracts\UI\ActionButtonsContract;
use MoonShine\Contracts\UI\HasFieldsContract;
use MoonShine\UI\Collections\ActionButtons;
use MoonShine\UI\Traits\HasDataCast;
use MoonShine\UI\Traits\WithFields;

abstract class RowComponent extends MoonShineComponent implements HasFieldsContract
{
    use HasDataCast;
    use WithFields;

    protected mixed $values = [];

    protected iterable $buttons = [];

    public function fill(mixed $values = []): static
    {
        $this->values = $values;

        return $this;
    }

    public function fillCast(mixed $values, DataCasterContract $cast): static
    {
        return $this
            ->cast($cast)
            ->fill($values);
    }

    public function getValues(): mixed
    {
        return $this->values ?? [];
    }

    public function buttons(iterable $buttons = []): static
    {
        $this->buttons = $buttons;

        return $this;
    }

    public function getPreparedFields(): FieldsContract
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

    public function getButtons(): ActionButtonsContract
    {
        return ActionButtons::make($this->buttons)
            ->fill($this->castData($this->getValues()))
            ->onlyVisible()
            ->withoutBulk();
    }
}
