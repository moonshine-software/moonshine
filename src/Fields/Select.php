<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use JsonException;
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

    /**
     * @throws JsonException
     */
    public function indexViewValue(Model $item, bool $container = true): string
    {
        if ($this->hasRelationship()) {
            return parent::indexViewValue($item, $container);
        }

        $value = $item->{$this->field()};

        if ($this->isMultiple()) {
            $value = is_string($value) ?
                json_decode($value, true, 512, JSON_THROW_ON_ERROR)
                : $value;

            return collect($value)
                ->when(
                    $container,
                    fn ($collect): Collection => $collect->map(
                        fn ($v): string => view('moonshine::ui.badge', [
                            'color' => 'purple',
                            'value' => $this->values()[$v] ?? false,
                        ])->render()
                    )
                )
                ->implode(',');
        }

        return (string) ($this->values()[$value] ?? '');
    }
}
