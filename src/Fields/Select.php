<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Support\Collection;
use JsonException;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeArray;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeNumeric;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeString;
use MoonShine\Contracts\Fields\HasDefaultValue;
use MoonShine\Contracts\Fields\HasUpdateOnPreview;
use MoonShine\Contracts\HasReactivity;
use MoonShine\Traits\Fields\CanBeMultiple;
use MoonShine\Traits\Fields\HasPlaceholder;
use MoonShine\Traits\Fields\Reactivity;
use MoonShine\Traits\Fields\Searchable;
use MoonShine\Traits\Fields\SelectTrait;
use MoonShine\Traits\Fields\UpdateOnPreview;
use MoonShine\Traits\Fields\WithDefaultValue;
use MoonShine\Traits\HasAsync;

class Select extends Field implements
    HasDefaultValue,
    DefaultCanBeArray,
    DefaultCanBeString,
    DefaultCanBeNumeric,
    HasUpdateOnPreview,
    HasReactivity
{
    use CanBeMultiple;
    use Searchable;
    use SelectTrait;
    use WithDefaultValue;
    use HasAsync;
    use UpdateOnPreview;
    use HasPlaceholder;
    use Reactivity;

    protected string $view = 'moonshine::fields.select';

    /**
     * @throws JsonException
     */
    protected function resolvePreview(): string
    {
        $value = $this->toValue();

        if ($this->isMultiple()) {
            $value = is_string($value) && str($value)->isJson() ?
                json_decode($value, true, 512, JSON_THROW_ON_ERROR)
                : $value;

            return collect($value)
                ->when(
                    ! $this->isRawMode(),
                    fn ($collect): Collection => $collect->map(
                        fn ($v): string => (string) data_get($this->getValues()->flatten(), "$v.label", '')
                    )
                )
                ->implode(',');
        }

        if (is_null($value)) {
            return '';
        }

        return (string) data_get($this->getValues()->flatten(), "$value.label", '');
    }

    protected function viewData(): array
    {
        return [
            'isSearchable' => $this->isSearchable(),
            'asyncUrl' => $this->asyncUrl(),
            'values' => $this->getValues()->toArray(),
            'isNullable' => $this->isNullable(),
        ];
    }
}
