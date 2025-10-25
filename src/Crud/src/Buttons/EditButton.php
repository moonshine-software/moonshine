<?php

declare(strict_types=1);

namespace MoonShine\Crud\Buttons;

use MoonShine\Contracts\Core\CrudResourceContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\Ability;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\UI\Components\ActionButton;
use Throwable;

final class EditButton
{
    /**
     * @param array<string, mixed> $query
     * @throws Throwable
     */
    public static function for(
        CrudResourceContract $resource,
        ?string $componentName = null,
        bool $isAsync = true,
        string $modalName = 'resource-edit-modal',
        array $query = []
    ): ActionButtonContract {
        if (! $resource->getFormPage()) {
            return ActionButton::emptyHidden();
        }

        $action = static fn (mixed $item, ?DataWrapperContract $data): string => $resource->getFormPageUrl($data?->getKey());

        // required to create field entities and load assets
        if (! $resource->isCreateInModal() && $resource->isEditInModal()) {
            $resource->getFormFields();
        }

        if ($resource->isEditInModal() && ! $resource->isDetailPage()) {
            $action = static fn (mixed $item, ?DataWrapperContract $data): string => $resource->getFormPageUrl(
                $data?->getKey(),
                array_filter([
                    '_component_name' => $componentName ?? $resource->getListComponentName(),
                    '_async_form' => $isAsync,
                    ...$query,
                ]),
                fragment: 'crud-form'
            );
        }

        return ActionButton::make(
            '',
            url: $action
        )
            ->name('resource-edit-button')
            ->withoutLoading()
            ->when(
                $resource->isEditInModal() && ! $resource->isDetailPage(),
                static fn (ActionButtonContract $button): ActionButtonContract => $button->async(
                    selector: "#$modalName",
                    events: [AlpineJs::event(JsEvent::MODAL_TOGGLED, $modalName)]
                )
            )
                ->primary()
                ->icon('pencil')
                ->canSee(
                    static fn (mixed $item, ?DataWrapperContract $data): bool => $data?->getKey()
                        && $resource->hasAction(Action::UPDATE)
                        && $resource->setItem($item)->can(Ability::UPDATE)
                )
                ->class('btn-square js-edit-button')
                ->showInLine();
    }
}
