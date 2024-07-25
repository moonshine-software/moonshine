<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Buttons;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Laravel\Enums\Ability;
use MoonShine\Laravel\Enums\Action;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\ActionButton;

final class EditButton
{
    public static function for(
        ModelResource $resource,
        ?string $componentName = null,
        bool $isAsync = true
    ): ActionButtonContract {
        if (! $resource->getFormPage()) {
            return ActionButton::emptyHidden();
        }

        $action = static fn ($data): string => $resource->getFormPageUrl($data);

        if ($resource->isEditInModal()) {
            $action = static fn ($data): string => $resource->getFormPageUrl(
                $data,
                params: [
                    '_component_name' => $componentName ?? $resource->getListComponentName(),
                    '_async_form' => $isAsync,
                ],
                fragment: 'crud-form'
            );
        }

        return ActionButton::make(
            '',
            url: $action
        )
            ->name('edit-button')
            ->when(
                $resource->isEditInModal(),
                static fn (ActionButtonContract $button): ActionButtonContract => $button->async()->inModal(
                    title: static fn (): array|string|null => __('moonshine::ui.edit'),
                    content: static fn (): string => '',
                    name: static fn (Model $data): string => "edit-modal-{$data->getKey()}"
                )
            )
            ->primary()
            ->icon('pencil')
            ->canSee(
                static fn (?Model $item): bool => ! is_null($item) && $resource->hasAction(Action::UPDATE)
                    && $resource->setItem($item)->can(Ability::UPDATE)
            )
            ->class('js-edit-button')
            ->showInLine();
    }
}
