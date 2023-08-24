<?php

declare(strict_types=1);

namespace MoonShine\Fields\Relationships;

use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeNumeric;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeString;
use MoonShine\Contracts\Fields\HasDefaultValue;
use MoonShine\Contracts\Fields\Relationships\HasRelatedValues;
use MoonShine\Pages\Crud\FormPage;
use MoonShine\Traits\Fields\Searchable;
use MoonShine\Traits\Fields\WithAsyncSearch;
use MoonShine\Traits\Fields\WithDefaultValue;
use MoonShine\Traits\Fields\WithRelatedValues;

class BelongsTo extends ModelRelationField implements
    HasRelatedValues,
    HasDefaultValue,
    DefaultCanBeString,
    DefaultCanBeNumeric
{
    use WithRelatedValues;
    use WithAsyncSearch;
    use Searchable;
    use WithDefaultValue;

    protected string $view = 'moonshine::fields.select';

    protected bool $toOne = true;

    protected function resolvePreview(): string
    {
        return $this->toValue() ? view('moonshine::ui.url', [
            'href' => to_page(
                $this->getResource(),
                FormPage::class,
                ['resourceItem' => $this->toValue()->getKey()]
            ),
            'withoutIcon' => true,
            'value' => parent::resolvePreview(),
        ])->render() : parent::resolvePreview();
    }

    protected function resolveValue(): mixed
    {
        return $this->toFormattedValue();
    }

    public function isSelected(string $value): bool
    {
        if (! $this->toValue()) {
            return false;
        }

        return (string) $this->toValue()->getKey() === $value;
    }
}
