<?php

declare(strict_types=1);

namespace MoonShine\Buttons\IndexPage;

use MoonShine\ActionButtons\ActionButton;
use MoonShine\Components\FormBuilder;
use MoonShine\Fields\HiddenIds;
use MoonShine\Resources\ModelResource;

final class MassDeleteButton
{
    public static function for(ModelResource $resource, string $tableName = 'default'): ActionButton
    {
        $action = static fn (): string => $resource->route('crud.massDelete');

        return ActionButton::make(
            '',
            url: $action
        )
            ->bulk()
            ->secondary()
            ->icon('heroicons.outline.trash')
            ->withConfirm(
                fields: fn (): array => [
                    HiddenIds::make(),
                ],
                method: 'DELETE',
                formBuilder: fn (FormBuilder $formBuilder) => $formBuilder->when(
                    $resource->isAsync(),
                    fn (FormBuilder $form): FormBuilder => $form->async(asyncEvents: 'table-updated-' . $tableName)
                )
            )
            ->canSee(
                fn (): bool => in_array('delete', $resource->getActiveActions())
                && $resource->can('massDelete')
            )
            ->showInLine();
    }
}
