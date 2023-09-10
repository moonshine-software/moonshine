<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeBool;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeNumeric;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeString;
use MoonShine\Contracts\Fields\HasDefaultValue;
use MoonShine\Traits\Fields\BooleanTrait;
use MoonShine\Traits\Fields\WithDefaultValue;

class Checkbox extends Field implements
    HasDefaultValue,
    DefaultCanBeNumeric,
    DefaultCanBeString,
    DefaultCanBeBool
{
    use BooleanTrait;
    use WithDefaultValue;

    protected string $view = 'moonshine::fields.checkbox';

    protected string $type = 'checkbox';

    public function isChecked(): bool
    {
        return (bool) $this->getOnValue() === $this->value();
    }

    protected function resolveValue(): mixed
    {
        $this->beforeLabel();
        $this->customWrapperAttributes([
            'class' => 'form-group-inline',
        ]);

        return parent::resolveValue();
    }

    protected function resolvePreview(): string
    {
        if ($this->isRawMode()) {
            return (string) ($this->toValue(false)
                ? $this->onValue
                : $this->offValue);
        }

        return view('moonshine::ui.boolean', [
            'value' => (bool) $this->toValue(false),
        ])->render();
    }
}
