<?php

declare(strict_types=1);

namespace MoonShine\Fields\Relationships;

use MoonShine\Components\FormBuilder;
use MoonShine\Fields\Fields;
use MoonShine\Fields\Hidden;
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

    protected function resolveValue(): mixed
    {
        $item = $this->toValue();
        $resource = $this->getResource();
        $fields = $this->hasFields()
            ? $this->getFields()->formFields()
            : $resource->getFormFields();

        return FormBuilder::make()
            ->fields(
                $fields->when(
                    ! is_null($item),
                    fn (Fields $fields): Fields => $fields->push(
                        Hidden::make('_method')->setValue('PUT')
                    )
                )->toArray()
            )
            ->fill($item?->attributesToArray() ?? [])
            ->cast($resource->getModelCast())
            ->submit(__('moonshine::ui.save'), ['class' => 'btn-primary btn-lg']);
    }
}
