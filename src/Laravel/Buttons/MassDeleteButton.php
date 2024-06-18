<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Buttons;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\Enums\HttpMethod;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\FormBuilder;

final class MassDeleteButton
{
    public static function for(
        ModelResource $resource,
        string $componentName = null,
        string $redirectAfterDelete = '',
        bool $isAsync = true,
    ): ActionButton {
        $action = static fn (): string => $resource->getRoute('crud.massDelete', query: [
            ...$redirectAfterDelete
                ? ['_redirect' => $redirectAfterDelete]
                : [],
        ]);

        return ActionButton::make(
            '',
            url: $action
        )
            ->bulk($componentName ?? $resource->getListComponentName())
            ->withConfirm(
                method: HttpMethod::DELETE,
                formBuilder: fn (FormBuilder $formBuilder) => $formBuilder->when(
                    $isAsync || $resource->isAsync(),
                    fn (FormBuilder $form): FormBuilder => $form->async(
                        events: $resource->getListEventName(
                            $componentName ?? $resource->getListComponentName()
                        )
                    )
                ),
                name: "mass-delete-modal-" . ($componentName ?? $resource->getListComponentName())
            )
            ->canSee(
                fn (): bool => in_array('massDelete', $resource->getActiveActions())
                    && $resource->can('massDelete')
            )
            ->error()
            ->icon('trash')
            ->showInLine();
    }
}
