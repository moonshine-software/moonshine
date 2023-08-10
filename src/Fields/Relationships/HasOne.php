<?php

declare(strict_types=1);

namespace MoonShine\Fields\Relationships;

use MoonShine\Components\FormBuilder;
use MoonShine\Fields\Fields;
use MoonShine\Fields\ID;
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
        $column = $this->getResource()->column();

        if ($this->isRawMode()) {
            return $values
                ->map(fn (Model $item) => $item->{$column})
                ->implode(';');
        }

        $fields = $this->getFields()
            ->indexFields()
            ->prepend(ID::make())
            ->toArray();

        return (string) table($fields, $values)
            ->cast($this->getModelCast())
            ->trAttributes(fn ($data, $row, $attr) => $attr)
            ->tdAttributes(function ($data, $row, $cell, $attr) {
                if ($cell === 0) {
                    return $attr->merge([
                        'class' => 'bgc-red',
                    ]);
                }

                if ($cell === 1) {
                    return $attr->merge([
                        'class' => 'bgc-green',
                    ]);
                }

                return $attr;
            })
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
