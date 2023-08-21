<?php

declare(strict_types=1);

namespace MoonShine\Fields\Relationships;

use MoonShine\Components\FormBuilder;
use MoonShine\Fields\Fields;
use MoonShine\Fields\Hidden;

class HasOne extends HasMany
{
    protected string $view = 'moonshine::fields.relationships.has-one';

    protected bool $toOne = true;

    protected function resolveValue(): mixed
    {
        $item = $this->toValue();
        $resource = $this->getResource();
        $fields = $this->preparedFields();

        $parentResource = moonshineRequest()->getResource();

        return FormBuilder::make($parentResource->route(
                name: is_null($item)
                        ? 'relation.store'
                        : 'relation.update',
                query: ['resourceUri' => $parentResource->uriKey(), 'resourceItem' => $parentResource->getItemID()]
            ))
            ->fields(
                $fields->when(
                    ! is_null($item),
                    fn (Fields $fields): Fields => $fields->push(
                        Hidden::make('_method')->setValue('PUT'),
                    )
                )->push(
                    Hidden::make('_relation')->setValue($this->getRelationName()),
                )->toArray()
            )
            ->formName($this->getRelationName())
            ->fill($item?->attributesToArray() ?? [])
            ->cast($resource->getModelCast())
            ->submit(__('moonshine::ui.save'), ['class' => 'btn-primary btn-lg']);
    }
}
