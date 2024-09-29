<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Buttons;

use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Laravel\Enums\Ability;
use MoonShine\Laravel\Enums\Action;
use MoonShine\Laravel\Resources\CrudResource;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\UI\Components\ActionButton;

final class EditButton
{
    public static function for(
        CrudResource $resource,
        ?string $componentName = null,
        bool $isAsync = true,
        string $modalName = 'resource-edit-modal',
    ): ActionButtonContract {
        if (! $resource->getFormPage()) {
            return ActionButton::emptyHidden();
        }

        $action = static fn (mixed $item, ?DataWrapperContract $data): string => $resource->getFormPageUrl($data?->getKey());

        if ($resource->isEditInModal()) {
            $action = static fn (mixed $item, ?DataWrapperContract $data): string => $resource->getFormPageUrl(
                $data?->getKey(),
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
                ->class('js-edit-button')
                ->showInLine();
    }
}
