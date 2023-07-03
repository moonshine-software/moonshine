<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
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

    public function indexViewValue(Model $item, bool $container = true): string
    {
        if (! $container) {
            return parent::indexViewValue($item, $container);
        }

        return view('moonshine::ui.boolean', [
            'value' => (bool) $this->formViewValue($item),
        ])->render();
    }

    public function exportViewValue(Model $item): string
    {
        return (string) ($this->formViewValue($item)
            ? $this->onValue
            : $this->offValue);
    }
}
