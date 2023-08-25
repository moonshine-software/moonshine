<?php

declare(strict_types=1);

namespace MoonShine\Fields\Relationships;

use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeNumeric;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeString;
use MoonShine\Contracts\Fields\HasDefaultValue;
use MoonShine\Contracts\Fields\Relationships\HasAsyncSearch;
use MoonShine\Contracts\Fields\Relationships\HasRelatedValues;
use MoonShine\Pages\Crud\FormPage;
use MoonShine\Traits\Fields\Searchable;
use MoonShine\Traits\Fields\WithAsyncSearch;
use MoonShine\Traits\Fields\WithDefaultValue;
use MoonShine\Traits\Fields\WithRelatedValues;

class BelongsTo extends ModelRelationField implements
    HasAsyncSearch,
    HasRelatedValues,
    HasDefaultValue,
    DefaultCanBeString,
    DefaultCanBeNumeric
{
    use WithRelatedValues;
    use WithAsyncSearch;
    use Searchable;
    use WithDefaultValue;

    protected string $view = 'moonshine::fields.relationships.belongs-to';

    protected bool $toOne = true;

    protected function resolvePreview(): string
    {
        if($this->toValue()) {
            $this->link(
                to_page(
                    $this->getResource(),
                    FormPage::class,
                    ['resourceItem' => $this->toValue()->getKey()]
                ),
                withoutIcon: true
            );
        }

        return parent::resolvePreview();
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
