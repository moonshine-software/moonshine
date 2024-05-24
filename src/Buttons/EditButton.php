<?php

declare(strict_types=1);

namespace MoonShine\Buttons;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Components\ActionButtons\ActionButton;
use MoonShine\Resources\ModelResource;

final class EditButton
{
    public static function for(
        ModelResource $resource,
        ?string $componentName = null,
        bool $isAsync = true
    ): ActionButton {
        if (! $resource->formPage()) {
            return ActionButton::emptyHidden();
        }

        $action = static fn ($data): string => $resource->formPageUrl($data);

        if ($resource->isEditInModal()) {
            $action = static fn ($data): string => $resource->formPageUrl(
                $data,
                params: [
                    '_component_name' => $componentName ?? $resource->listComponentName(),
                    '_async_form' => $isAsync,
                ],
                fragment: 'crud-form'
            );
        }

        return ActionButton::make(
            '',
            url: $action
        )
            ->when(
                $resource->isEditInModal(),
                fn (ActionButton $button): ActionButton => $button->async()->inModal(
                    title: fn (): array|string|null => __('moonshine::ui.edit'),
                    content: fn (): string => '',
                    name: fn (Model $data): string => "edit-modal-{$data->getKey()}"
                )
            )
            ->primary()
            ->icon('pencil')
            ->canSee(
                fn (?Model $item): bool => ! is_null($item) && in_array('update', $resource->getActiveActions())
                    && $resource->setItem($item)->can('update')
            )
            ->customAttributes(['class' => 'edit-button'])
            ->showInLine();
    }
}
