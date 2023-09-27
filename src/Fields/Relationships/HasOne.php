<?php

declare(strict_types=1);

namespace MoonShine\Fields\Relationships;

use MoonShine\ActionButtons\ActionButton;
use MoonShine\Components\FormBuilder;
use MoonShine\Decorations\TextBlock;
use MoonShine\Exceptions\FieldException;
use MoonShine\Fields\Fields;
use MoonShine\Fields\Hidden;
use Throwable;

class HasOne extends HasMany
{
    protected bool $toOne = true;

    public function value(bool $withOld = true): mixed
    {
        $this->setValue($this->getRelatedModel()->{$this->getRelationName()});

        return ModelRelationField::value($withOld);
    }

    /**
     * @throws FieldException
     * @throws Throwable
     */
    protected function resolveValue(): mixed
    {
        $resource = $this->getResource();

        $parentResource = moonshineRequest()->getResource();

        $item = $this->toValue();

        if(is_null($parentResource)) {
            throw new FieldException('Parent resource is required');
        }

        $parentItem = $parentResource->getItemOrInstance();

        $fields = $this->preparedFields();

        $action = to_relation_route(
            is_null($item) ? 'store' : 'update',
            $this->getRelatedModel()?->getKey(),
        );

        return FormBuilder::make($action)
            ->precognitive()
            ->async()
            ->name($this->getRelationName())
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
            ->fill($item?->attributesToArray() ?? [])
            ->cast($resource->getModelCast())
            ->buttons(is_null($item) ? [] : [
                ActionButton::make(
                    __('moonshine::ui.delete'),
                    url: fn ($data): string => $resource->route('crud.destroy', $data->getKey())
                )
                    ->customAttributes(['class' => 'btn-secondary btn-lg'])
                    ->inModal(
                        fn (): array|string|null => __('moonshine::ui.delete'),
                        fn (ActionButton $action): string => (string) form(
                            $action->url(),
                            fields: [
                                Hidden::make('_method')->setValue('DELETE'),
                                TextBlock::make('', __('moonshine::ui.confirm_message')),
                            ]
                        )
                            ->submit(__('moonshine::ui.delete'), ['class' => 'btn-secondary'])
                            ->redirect(
                                to_page(
                                    $parentResource,
                                    'form-page',
                                    ['resourceItem' => $parentItem->getKey()]
                                )
                            )
                    )
                    ->showInLine(),
            ])
            ->submit(__('moonshine::ui.save'), ['class' => 'btn-primary btn-lg']);
    }
}
