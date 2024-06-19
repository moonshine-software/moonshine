<?php

namespace MoonShine\Laravel\Buttons;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\ActionButton;

final class CreateButton
{
    public static function for(
        ModelResource $resource,
        ?string $componentName = null,
        bool $isAsync = true,
    ): ActionButton {
        if(! $resource->getFormPage()) {
            return ActionButton::emptyHidden();
        }

        $action = $resource->getFormPageUrl();

        if($resource->isCreateInModal()) {
            $action = $resource->getFormPageUrl(
                params: [
                    '_component_name' => $componentName ?? $resource->getListComponentName(),
                    '_async_form' => $isAsync,
                ],
                fragment: 'crud-form'
            );
        }

        return ActionButton::make(
            __('moonshine::ui.create'),
            $action
        )
            ->when(
                $resource->isCreateInModal(),
                static fn (ActionButton $button): ActionButton => $button->async()->inModal(
                    static fn (): array|string|null => __('moonshine::ui.create'),
                    static fn (): string => '',
                )
            )
            ->canSee(
                static fn (): bool => in_array('create', $resource->getActiveActions())
                && $resource->can('create')
            )
            ->primary()
            ->icon('plus');
    }
}
