<?php

declare(strict_types=1);

namespace MoonShine\Crud\Buttons;

use MoonShine\Contracts\Core\CrudResourceContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\ModalContract;
use MoonShine\Support\Enums\Ability;
use MoonShine\Support\Enums\Action;
use MoonShine\UI\Components\ActionButton;
use Throwable;

final class CreateButton
{
    /**
     * @throws Throwable
     */
    public static function for(
        string $label,
        CrudResourceContract $resource,
        ?string $componentName = null,
        bool $isAsync = true,
        string $modalName = 'resource-create-modal',
    ): ActionButtonContract {
        if (! $resource->getFormPage()) {
            return ActionButton::emptyHidden();
        }

        $action = $resource->getFormPageUrl();

        if ($resource->isCreateInModal()) {
            // required to create field entities and load assets
            $resource->getFormFields();

            $action = $resource->getFormPageUrl(
                params: [
                    '_component_name' => $componentName ?? $resource->getListComponentName(),
                    '_async_form' => $isAsync,
                ],
                fragment: 'crud-form'
            );
        }

        return ActionButton::make(
            $label,
            $action
        )
            ->name('resource-create-button')
            ->when(
                $resource->isCreateInModal(),
                static fn (ActionButtonContract $button): ActionButtonContract => $button->async()->inModal(
                    static fn (): string => $label,
                    static fn (): string => '',
                    name: $modalName,
                    builder: static fn (ModalContract $modal): ModalContract => $resource->resolveCreateModal($modal),
                )
            )
            ->canSee(
                static fn (): bool => $resource->hasAction(Action::CREATE)
                && $resource->can(Ability::CREATE)
            )
            ->primary()
            ->icon('plus');
    }
}
