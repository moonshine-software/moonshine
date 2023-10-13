<?php

declare(strict_types=1);

namespace MoonShine\Components;

use MoonShine\ActionButtons\ActionButtons;
use MoonShine\Contracts\MoonShineDataCast;
use MoonShine\Fields\Fields;
use MoonShine\Traits\HasDataCast;
use MoonShine\Traits\WithFields;

abstract class RowComponent extends MoonshineComponent
{
    use HasDataCast;
    use WithFields;

    protected mixed $values = [];

    protected array $buttons = [];

    public function fill(mixed $values = []): static
    {
        if($this->hasCast() && filled($values)) {
            $class = $this->getCast()->getClass();

            $values = !$values instanceof $class
                ? $this->getCast()->hydrate($values)
                : $values;

            $values = $this->getCast()->dehydrate($values);
        }

        $this->values = $values ?? [];

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
            ->fillItem($casted)
            ->onlyVisible();
    }
}
