<?php

declare(strict_types=1);

namespace MoonShine\Crud\Buttons;

use MoonShine\Contracts\Core\CrudResourceContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\Contracts\UI\ModalContract;
use MoonShine\Support\Enums\Ability;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\Enums\HttpMethod;
use MoonShine\UI\Components\ActionButton;

final class DeleteButton
{
    /**
     * @param array<string, mixed> $query
     */
    public static function for(
        CrudResourceContract $resource,
        ?string $componentName = null,
        ?string $redirectAfterDelete = null,
        bool $isAsync = true,
        string $modalName = 'resource-delete-modal',
        array $query = [],
    ): ActionButtonContract {
        $action = static fn (mixed $item, ?DataWrapperContract $data): string => $resource->getRoute(
            'crud.destroy',
            $data?->getKey(),
            $redirectAfterDelete
            ? ['_redirect' => $redirectAfterDelete]
            : []
        );

        return ActionButton::make(
            '',
            url: $action
        )
            ->name('resource-delete-button')
            ->withoutLoading()
            ->withConfirm(
                method: HttpMethod::DELETE,
                formBuilder: static fn (FormBuilderContract $formBuilder): FormBuilderContract => $formBuilder->when(
                    $isAsync || $resource->isAsync(),
                    static fn (FormBuilderContract $form): FormBuilderContract => $form->async(
                        events: $resource->getListEventName(
                            $componentName ?? $resource->getListComponentName(),
                            params: $query,
                        )
                    ),
                ),
                modalBuilder: static fn (ModalContract $modal): ModalContract => $resource->resolveDeleteModal($modal),
                name: static fn (mixed $item, ActionButtonContract $ctx): string => "$modalName-{$ctx->getData()?->getKey()}",
            )
            ->canSee(
                static fn (mixed $item, ?DataWrapperContract $data): bool => $data?->getKey()
                    && $resource->hasAction(Action::DELETE)
                    && $resource->setItem($item)->can(Ability::DELETE)
            )
            ->error()
            ->icon('trash')
            ->class('btn-square')
            ->showInLine();
    }
}
