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
