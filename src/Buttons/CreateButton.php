<?php

namespace MoonShine\Buttons;

use MoonShine\ActionButtons\ActionButton;
use MoonShine\Resources\ModelResource;

final class CreateButton
{
    public static function for(
        ModelResource $resource,
        ?string $componentName = null,
        bool $isAsync = false,
    ): ActionButton {
        if(! $resource->formPage()) {
            return ActionButton::emptyHidden();
        }

        $action = $resource->formPageUrl();

        if($resource->isCreateInModal()) {
            $action = $resource->formPageUrl(
                params: [
                    '_component_name' => $componentName ?? $resource->listComponentName(),
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
                fn (ActionButton $button): ActionButton => $button->inModal(
                    fn (): array|string|null => __('moonshine::ui.create'),
                    fn (): string => '',
                    async: true
                )
            )
            ->canSee(
                fn (): bool => in_array('create', $resource->getActiveActions())
                && $resource->can('create')
            )
            ->primary()
            ->icon('heroicons.outline.plus');
    }
}
