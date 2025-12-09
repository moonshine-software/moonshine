<?php

declare(strict_types=1);

namespace MoonShine\Crud\Buttons;

use MoonShine\Contracts\Core\CrudResourceContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\Contracts\UI\ModalContract;
use MoonShine\Support\Enums\Ability;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\Enums\HttpMethod;
use MoonShine\UI\Components\ActionButton;

final class MassDeleteButton
{
    /**
     * @param array<string, mixed> $query
     */
    public static function for(
        CrudResourceContract $resource,
        ?string $componentName = null,
        ?string $redirectAfterDelete = null,
        bool $isAsync = true,
        string $modalName = 'resource-mass-delete-modal',
        array $query = []
    ): ActionButtonContract {
        $action = static fn (): string => $resource->getRoute('crud.massDelete', query: [
            ...$redirectAfterDelete
                ? ['_redirect' => $redirectAfterDelete]
                : [],
        ]);

        return ActionButton::make(
            '',
            url: $action
        )
            ->name('mass-delete-button')
            ->bulk($componentName ?? $resource->getListComponentName())
            ->withConfirm(
                method: HttpMethod::DELETE,
                formBuilder: static fn (FormBuilderContract $formBuilder): FormBuilderContract => $formBuilder->when(
                    $isAsync || $resource->isAsync(),
                    static fn (FormBuilderContract $form): FormBuilderContract => $form->async(
                        events: $resource->getListEventName(
                            $componentName ?? $resource->getListComponentName(),
                            params: $query
                        )
                    )
                ),
                modalBuilder: static fn (ModalContract $modal): ModalContract => $resource->resolveMassDeleteModal($modal),
                name: $modalName
            )
            ->canSee(
                static fn (): bool => $resource->hasAction(Action::MASS_DELETE)
                  && $resource->can(Ability::MASS_DELETE)
            )
            ->error()
            ->icon('trash')
            ->class('btn-square')
            ->showInLine();
    }
}
