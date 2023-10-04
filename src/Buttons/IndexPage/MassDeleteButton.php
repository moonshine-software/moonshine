<?php

declare(strict_types=1);

namespace MoonShine\Buttons\IndexPage;

use Illuminate\Database\Eloquent\Model;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Decorations\Heading;
use MoonShine\Fields\Hidden;
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
                        Hidden::actionCheckedIds(),
                        Heading::make(__('moonshine::ui.confirm_message')),
                    ])
                    ->submit('Delete', ['class' => 'btn-secondary'])
            )
            ->canSee(
                fn (?Model $item): bool => ! is_null($item) && in_array('delete', $resource->getActiveActions())
                && $resource->setItem($item)->can('massDelete')
            )
            ->showInLine();
    }
}
