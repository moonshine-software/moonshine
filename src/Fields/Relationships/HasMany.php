<?php

declare(strict_types=1);

namespace MoonShine\Fields\Relationships;

use MoonShine\Components\TableBuilder;
use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Fields\Fields;
use MoonShine\Traits\WithFields;
use Throwable;

class HasMany extends ModelRelationField implements HasFields
{
    use WithFields;

    protected string $view = 'moonshine::fields.relationships.has-many';

    protected bool $isGroup = true;

    protected bool $outsideComponent = true;

    /**
     * @throws Throwable
     */
    protected function prepareFields(Fields $fields): Fields
    {
        if ($fields->isEmpty()) {
            $this->fields(
                $this->getResource()
                    ?->getIndexFields()
                    ?->toArray() ?? []
            );

            return Fields::make($this->fields);
        }

        return $fields;
    }

    protected function resolvePreview(): string
    {
        $values = $this->toValue();
        $column = $this->getResource()->column();

        if ($this->isRawMode()) {
            return $values
                ->map(fn (Model $item) => $item->{$column})
                ->implode(';');
        }

        $fields = $this->getFields()
            ->indexFields()
            ->toArray();

        return (string) table($fields, $values)
            ->cast($this->getModelCast())
            ->preview();
    }

    protected function resolveValue(): mixed
    {
        return table($this->getFields()->toArray(), $this->toValue() ?? [])
            ->when(
                $this->getRelation(),
                fn ($table): TableBuilder => $table->cast($this->getModelCast())
            )
            ->withNotFound()
            ->preview();
    }
}
