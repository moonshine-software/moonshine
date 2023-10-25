<?php

declare(strict_types=1);

namespace MoonShine\Buttons\IndexPage;

use MoonShine\ActionButtons\ActionButton;
use MoonShine\Components\FormBuilder;
use MoonShine\Decorations\Heading;
use MoonShine\Fields\Hidden;
use MoonShine\Fields\HiddenIds;
use MoonShine\Resources\ModelResource;

final class MassDeleteButton
{
    public static function for(ModelResource $resource, string $tableName = 'default'): ActionButton
    {
        return ActionButton::make(
            '',
            url: fn (): string => $resource->route('crud.massDelete')
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
                        fn (FormBuilder $form): FormBuilder => $form->async(asyncEvents: 'table-updated-' . $tableName)
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
