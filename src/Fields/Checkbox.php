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

    protected static string $view = 'moonshine::fields.checkbox';

    protected string $type = 'checkbox';

    public function preview(): string
    {
        if (! false) { // $container
            return parent::preview();
        }

        return view('moonshine::ui.boolean', [
            'value' => (bool) $this->value(),
        ])->render();
    }

    public function exportViewValue(): string
    {
        return (string) ($this->value()
            ? $this->onValue
            : $this->offValue);
    }
}
