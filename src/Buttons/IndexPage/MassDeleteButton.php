<?php

declare(strict_types=1);

namespace MoonShine\Buttons\IndexPage;

use MoonShine\ActionButtons\ActionButton;
use MoonShine\Decorations\Heading;
use MoonShine\Fields\Hidden;
use MoonShine\Fields\HiddenIds;
use MoonShine\Resources\ModelResource;

final class MassDeleteButton
{
    public static function for(ModelResource $resource): ActionButton
    {
        return ActionButton::make(
            '',
            url: fn (): string => route('moonshine.crud.destroy', [
                'resourceUri' => $resource->uriKey(),
                'resourceItem' => 0,
            ])
        )
            ->bulk()
            ->secondary()
            ->icon('heroicons.outline.trash')
            ->inModal(
                fn (): string => 'Delete',
                fn (): string => (string) form($resource->route('crud.massDelete'))
                    ->fields([
                        Hidden::make('_method')->setValue('DELETE'),
                        HiddenIds::make(),
                        Heading::make(__('moonshine::ui.confirm_message')),
                    ])
                    ->submit('Delete', ['class' => 'btn-secondary'])
            )
            ->canSee(
                fn (): bool => in_array('delete', $resource->getActiveActions())
                && $resource->can('massDelete')
            )
            ->showInLine();
    }
}
