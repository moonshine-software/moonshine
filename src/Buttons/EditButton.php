<?php

declare(strict_types=1);

namespace MoonShine\Buttons;

use Illuminate\Database\Eloquent\Model;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Resources\ModelResource;

final class EditButton
{
    public static function for(
        ModelResource $resource,
        ?string $componentName = null,
        bool $isAsync = false
    ): ActionButton {
        if (! $resource->formPage()) {
            return ActionButton::emptyHidden();
        }

        $action = static fn ($data): string => $resource->formPageUrl($data);

        if ($resource->isEditInModal()) {
            $action = static fn ($data): string => $resource->formPageUrl(
                $data,
                params: array_filter([
                    '_component_name' => $componentName ?? $resource->listComponentName(),
                    '_async_form' => $isAsync,
                    'page' => $isAsync ? request()->input('page') : null,
                    'sort' => $isAsync ? request()->input('sort') : null,
                ]),
                fragment: 'crud-form'
            );
        }

        return ActionButton::make(
            '',
            url: $action
        )
            ->when(
                $resource->isEditInModal(),
                fn (ActionButton $button): ActionButton => $button->inModal(
                    fn (): array|string|null => __('moonshine::ui.edit'),
                    fn (): string => '',
                    async: true
                )
            )
            ->primary()
            ->icon('heroicons.outline.pencil')
            ->canSee(
                fn (?Model $item): bool => ! is_null($item) && in_array('update', $resource->getActiveActions())
                    && $resource->setItem($item)->can('update')
            )
            ->customAttributes(['class' => 'edit-button'])
            ->showInLine();
    }
}
