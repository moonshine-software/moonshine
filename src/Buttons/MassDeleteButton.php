<?php

declare(strict_types=1);

namespace MoonShine\Buttons;

use MoonShine\ActionButtons\ActionButton;
use MoonShine\Components\FormBuilder;
use MoonShine\Resources\ModelResource;

final class MassDeleteButton
{
    public static function for(
        ModelResource $resource,
        string $componentName = null,
        string $redirectAfterDelete = '',
        bool $isAsync = false,
    ): ActionButton {
        $action = static fn (): string => $resource->route('crud.massDelete', query: [
            ...$redirectAfterDelete
                ? ['_redirect' => $redirectAfterDelete]
                : [],
        ]);

        return ActionButton::make(
            '',
            url: $action
        )
            ->bulk($componentName ?? $resource->listComponentName())
            ->withConfirm(
                method: 'DELETE',
                formBuilder: fn (FormBuilder $formBuilder) => $formBuilder->when(
                    $isAsync || $resource->isAsync(),
                    fn (FormBuilder $form): FormBuilder => $form->async(
                        asyncEvents: $resource->listEventName(
                            $componentName ?? $resource->listComponentName(),
                            $isAsync ? array_filter([
                                    'page' => request()->input('page'),
                                    'sort' => request()->input('sort'),
                                ]) : []
                        )
                    )
                )
            )
            ->canSee(
                fn (): bool => in_array('massDelete', $resource->getActiveActions())
                    && $resource->can('massDelete')
            )
            ->error()
            ->icon('heroicons.outline.trash')
            ->showInLine();
    }
}
