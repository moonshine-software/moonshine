<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeBool;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeNumeric;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeString;
use MoonShine\Contracts\Fields\HasDefaultValue;
use MoonShine\Traits\Fields\BooleanTrait;
use MoonShine\Traits\Fields\CheckboxTrait;
use MoonShine\Traits\Fields\WithDefaultValue;

class Checkbox extends Field implements
    HasDefaultValue,
    DefaultCanBeNumeric,
    DefaultCanBeString,
    DefaultCanBeBool
{
    use CheckboxTrait;
    use BooleanTrait;
    use WithDefaultValue;

    protected string $view = 'moonshine::fields.checkbox';

    protected string $type = 'checkbox';

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
