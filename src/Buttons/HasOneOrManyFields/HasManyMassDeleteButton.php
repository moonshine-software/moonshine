<?php

namespace MoonShine\Buttons\HasOneOrManyFields;

use MoonShine\ActionButtons\ActionButton;
use MoonShine\Components\FormBuilder;
use MoonShine\Fields\HiddenIds;
use MoonShine\Fields\Relationships\HasMany;
use MoonShine\Resources\ModelResource;

final class HasManyMassDeleteButton
{
    public static function for(HasMany $field, ModelResource $resource, int|string $resourceItem): ActionButton
    {
        $action = static fn (): string => $resource->route('crud.massDelete', query: [
            '_redirect' => to_page(
                page: $resource->formPage(),
                resource: moonshineRequest()->getResource(),
                params: ['resourceItem' => $resourceItem]
            ),
        ]);

        return ActionButton::make(
            '',
            url: $action
        )
            ->bulk()
            ->secondary()
            ->icon('heroicons.outline.trash')
            ->withConfirm(
                fields: fn(): array => [
                    HiddenIds::make(),
                ],
                method: 'DELETE',
                formBuilder: fn(FormBuilder $formBuilder) => $formBuilder->when(
                    $field->isAsync() || $resource->isAsync(),
                    fn (FormBuilder $form): FormBuilder => $form
                        ->async(asyncEvents: 'table-updated-' . $field->getRelationName())
                )
            )
            ->canSee(
                fn (): bool => in_array('delete', $resource->getActiveActions())
                    && $resource->can('massDelete')
            )
            ->showInLine();
    }
}
