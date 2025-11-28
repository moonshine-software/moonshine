<?php

declare(strict_types=1);

namespace MoonShine\Crud\Buttons;

use MoonShine\Contracts\Core\CrudResourceContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\ModalContract;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\Ability;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Exceptions\ActionButtonException;

final class DetailButton
{
    public static function for(
        string $label,
        CrudResourceContract $resource,
        string $modalName = 'resource-detail-modal',
        bool $isSeparateModal = true,
    ): ActionButtonContract {
        if (! $resource->getDetailPage()) {
            return ActionButton::emptyHidden();
        }

        $action = static fn (mixed $item, ?DataWrapperContract $data): string => $data?->getKey() !== null
            ? $resource->getDetailPageUrl($data->getKey())
            : throw ActionButtonException::dataRequired();

        if ($resource->isDetailInModal()) {
            $action = static fn (mixed $item, ?DataWrapperContract $data): string => $data?->getKey() !== null ? $resource->getDetailPageUrl(
                $data->getKey(),
                fragment: 'crud-detail'
            ) : throw ActionButtonException::dataRequired();
        }

        return ActionButton::make(
            '',
            $action
        )
            ->name('resource-detail-button')
            ->withoutLoading()
            ->when(
                $resource->isDetailInModal() && $isSeparateModal,
                static fn (ActionButtonContract $button): ActionButtonContract => $button->async(
                    selector: "#$modalName",
                    events: [AlpineJs::event(JsEvent::MODAL_TOGGLED, $modalName)]
                )
            )
            ->when(
                $resource->isDetailInModal() && ! $isSeparateModal,
                static fn (ActionButtonContract $button): ActionButtonContract => $button->async()->inModal(
                    title: static fn (): string => $label,
                    content: static fn (): string => '',
                    name: static fn (mixed $data, ActionButtonContract $ctx): string => "$modalName-{$ctx->getData()?->getKey()}",
                    builder: static fn (ModalContract $modal): ModalContract => $resource->resolveDetailModal($modal)
                )
            )
            ->canSee(
                static fn (mixed $item, ?DataWrapperContract $data): bool => $data?->getKey()
                    && $resource->hasAction(Action::VIEW)
                    && $resource->setItem($item)->can(Ability::VIEW)
            )
            ->icon('eye')
            ->square()
            ->class('js-detail-button')
            ->showInLine();
    }
}
