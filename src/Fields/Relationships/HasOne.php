<?php

declare(strict_types=1);

namespace MoonShine\Fields\Relationships;

use MoonShine\ActionButtons\ActionButton;
use MoonShine\Components\FormBuilder;
use MoonShine\Decorations\TextBlock;
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

        return FormBuilder::make(
            $parentResource->route(
                is_null($item) ? 'relation.store' : 'relation.update',
                $parentResource->getItemID()
            )
        )
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
            ->name($this->getRelationName())
            ->fill($item?->attributesToArray() ?? [])
            ->cast($resource->getModelCast())
            ->buttons(is_null($item) ? [] : [
                ActionButton::make(
                    __('moonshine::ui.delete'),
                    url: fn ($data): string => $resource->route('crud.destroy', $data->getKey())
                )
                    ->customAttributes(['class' => 'btn-pink btn-lg'])
                    ->inModal(
                        fn (): array|string|null => __('moonshine::ui.delete'),
                        fn (ActionButton $action): string => (string) form(
                            $action->url(),
                            fields: [
                                Hidden::make('_method')->setValue('DELETE'),
                                TextBlock::make('', __('moonshine::ui.confirm_message')),
                            ]
                        )
                            ->submit(__('moonshine::ui.delete'), ['class' => 'btn-pink'])
                            ->redirect(
                                to_page(
                                    $parentResource,
                                    'form-page',
                                    ['resourceItem' => $parentResource->getItem()]
                                )
                            )
                    )
                    ->showInLine(),
            ])
            ->submit(__('moonshine::ui.save'), ['class' => 'btn-primary btn-lg']);
    }
}
