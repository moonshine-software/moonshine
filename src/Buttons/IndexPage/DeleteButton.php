<?php

declare(strict_types=1);

namespace MoonShine\Buttons\IndexPage;

use Illuminate\Database\Eloquent\Model;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Components\FormBuilder;
use MoonShine\Decorations\TextBlock;
use MoonShine\Fields\Hidden;
use MoonShine\Resources\ModelResource;

final class DeleteButton
{
    public static function for(ModelResource $resource, string $redirectAfterDelete = ''): ActionButton
    {
        return ActionButton::make(
            '',
            url: static fn ($data): string => route(
                'moonshine.crud.destroy',
                array_filter([
                    'resourceUri' => $resource->uriKey(),
                    'resourceItem' => $data->getKey(),
                    ...$redirectAfterDelete
                        ? ['_redirect' => $redirectAfterDelete]
                        : [],
                ])
            )
        )
            ->secondary()
            ->icon('heroicons.outline.trash')
            ->inModal(
                fn (): array|string|null => __('moonshine::ui.delete'),
                fn (ActionButton $action): string => (string) form(
                    $action->url(),
                    fields: [
                        Hidden::make('_method')->setValue('DELETE'),
                        TextBlock::make('', __('moonshine::ui.confirm_message')),
                    ]
                )
                    ->when($resource->isAsync() && $resource->isNowOnIndex(),
                        fn(FormBuilder $form) => $form->async(asyncEvents: 'table-updated')
                    )
                    ->submit(__('moonshine::ui.delete'), ['class' => 'btn-secondary'])
            )
            ->canSee(
                fn (?Model $item): bool => ! is_null($item) && in_array('delete', $resource->getActiveActions())
                && $resource->setItem($item)->can('delete')
            )
            ->showInLine();
    }
}
