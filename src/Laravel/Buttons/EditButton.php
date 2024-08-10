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
        bool $isAsync = true,
        string $modalName = 'delete-modal',
    ): ActionButtonContract {
        if (! $resource->getFormPage()) {
            return ActionButton::emptyHidden();
        }

        $action = static fn ($data): string => $resource->getFormPageUrl($data);

        if ($resource->isEditInModal()) {
            $action = static fn ($data): string => $resource->getFormPageUrl(
                $data,
                array_filter([
                    '_component_name' => $componentName ?? $resource->getListComponentName(),
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
            ->name('edit-button')
            ->when(
                $resource->isEditInModal(),
                static fn (ActionButtonContract $button): ActionButtonContract => $button->async()->inModal(
                    title: static fn (): array|string|null => __('moonshine::ui.edit'),
                    content: static fn (): string => '',
                    name: static fn (Model $data): string => "$modalName-{$data->getKey()}"
                )
            )
            ->primary()
            ->icon('pencil')
            ->canSee(
                static fn (?Model $item): bool => ! is_null($item) && $item->exists
                    && $resource->hasAction(Action::UPDATE)
                    && $resource->setItem($item)->can(Ability::UPDATE)
            )
            ->class('js-edit-button')
            ->showInLine();
    }
}
