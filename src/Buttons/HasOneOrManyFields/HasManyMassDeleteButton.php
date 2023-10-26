<?php

namespace MoonShine\Buttons\HasOneOrManyFields;

use MoonShine\ActionButtons\ActionButton;
use MoonShine\Components\FormBuilder;
use MoonShine\Decorations\Heading;
use MoonShine\Fields\Hidden;
use MoonShine\Fields\HiddenIds;
use MoonShine\Fields\Relationships\HasMany;
use MoonShine\Resources\ModelResource;

final class HasManyMassDeleteButton
{
    public static function for(HasMany $field, ModelResource $resource, int|string $resourceItem): ActionButton
    {
        return ActionButton::make(
            '',
            url: fn (): string => $resource->route('crud.massDelete', query: [
                '_redirect' => to_page(
                    page: $resource->formPage(),
                    resource: moonshineRequest()->getResource(),
                    params: ['resourceItem' => $resourceItem]
                ),
            ])
        )
            ->bulk()
            ->secondary()
            ->icon('heroicons.outline.trash')
            ->inModal(
                fn (): string => 'Delete',
                fn (ActionButton $action): string => (string) form($action->url())
                    ->fields([
                        Hidden::make('_method')->setValue('DELETE'),
                        HiddenIds::make(),
                        Heading::make(__('moonshine::ui.confirm_message')),
                    ])
                    ->when(
                        $resource->isAsync(),
                        fn (FormBuilder $form): FormBuilder => $form->async(asyncEvents: 'table-updated-' . $field->getRelationName())
                    )
                    ->submit('Delete', ['class' => 'btn-secondary'])
            )
            ->canSee(
                fn (): bool => in_array('delete', $resource->getActiveActions())
                    && $resource->can('massDelete')
            )
            ->showInLine();
    }
}
