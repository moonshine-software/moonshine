<?php

declare(strict_types=1);

namespace MoonShine\Fields\Relationships;

use Illuminate\View\ComponentAttributeBag;
use MoonShine\Components\FormBuilder;
use MoonShine\Fields\Fields;
use Throwable;

class HasOne extends HasMany
{
    protected string $view = 'moonshine::fields.relationships.has-one';

    protected bool $toOne = true;

    /**
     * @throws Throwable
     */
    protected function prepareFields(Fields $fields): Fields
    {
        if ($fields->isEmpty()) {
            $this->fields(
                $this->getResource()
                    ?->getFormFields()
                    ?->toArray() ?? []
            );

            return Fields::make($this->fields);
        }

        return $fields;
    }

    protected function resolvePreview(): string
    {
        if (is_null($this->toValue())) {
            return '';
        }

        $this->setValue(
            collect([
                $this->toValue(),
            ])
        );

        $values = $this->toValue();
        $column = $this->getResourceColumn();

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
            ->tdAttributes(fn (
                $data,
                int $row,
                int $cell,
                ComponentAttributeBag $attributes
            ): ComponentAttributeBag => $attributes->when(
                $cell === 0,
                fn (ComponentAttributeBag $attr): ComponentAttributeBag => $attr->merge([
                    'class' => 'font-semibold',
                    'width' => '20%',
                ])
            ))
            ->vertical()
            ->preview();
    }

    protected function resolveValue(): mixed
    {
        return form()
            ->when(
                $this->getRelation(),
                fn ($table): FormBuilder => $table->cast($this->getModelCast())
            )
            ->fill($this->toValue()?->toArray() ?? [])
            ->fields($this->getFields()->toArray());
    }
}
