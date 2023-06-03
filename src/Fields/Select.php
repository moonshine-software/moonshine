<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeArray;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeNumeric;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeString;
use MoonShine\Contracts\Fields\HasDefaultValue;
use MoonShine\Traits\Fields\CanBeMultiple;
use MoonShine\Traits\Fields\Searchable;
use MoonShine\Traits\Fields\SelectTrait;
use MoonShine\Traits\Fields\WithDefaultValue;

class Select extends Field implements
    HasDefaultValue,
    DefaultCanBeArray,
    DefaultCanBeString,
    DefaultCanBeNumeric
{
    use CanBeMultiple;
    use Searchable;
    use SelectTrait;
    use WithDefaultValue;

    protected static string $view = 'moonshine::fields.select';

    public function indexViewValue(Model $item, bool $container = true): string
    {
        $value = $item->{$this->field()};

        if ($this->isMultiple()) {
            if (is_string($value)) {
                $value = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
            }

            return collect($value)->map(
                fn ($v): string => view('moonshine::ui.badge', [
                    'color' => 'purple',
                    'value' => $this->values()[$v] ?? false,
                ])->render()
            )->implode(',');
        }

        return (string) (
            $this->values()[$item->{$this->field()}]
            ?? parent::indexViewValue($item, $container)
        );
    }
}
