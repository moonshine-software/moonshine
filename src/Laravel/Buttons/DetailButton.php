<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Buttons;

use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Laravel\Enums\Ability;
use MoonShine\Laravel\Enums\Action;
use MoonShine\Laravel\Resources\CrudResource;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Modal;

final class DetailButton
{
    public static function for(
        CrudResource $resource,
        string $modalName = 'detail-modal',
    ): ActionButtonContract {
        if (! $resource->getDetailPage()) {
            return ActionButton::emptyHidden();
        }

        $action = static fn (mixed $item, ?DataWrapperContract $data): string => $resource->getDetailPageUrl($data?->getKey());

        if ($resource->isDetailInModal()) {
            $action = static fn (mixed $item, ?DataWrapperContract $data): string => $resource->getDetailPageUrl(
                $data?->getKey(),
                fragment: 'crud-detail'
            );
        }

        return ActionButton::make(
            '',
            $action
        )
            ->name('detail-button')
            ->when(
                $resource->isDetailInModal(),
                static fn (ActionButtonContract $button): ActionButtonContract => $button->async()->inModal(
                    title: static fn (): array|string|null => __('moonshine::ui.show'),
                    content: static fn (): string => '',
                    name: static fn (mixed $data, ActionButtonContract $ctx): string => "$modalName-{$ctx->getData()?->getKey()}",
                    builder: static fn (Modal $modal): Modal => $modal->wide()
                )
            )
            ->canSee(
                static fn (mixed $item, ?DataWrapperContract $data): bool => $data?->getKey()
                    && $resource->hasAction(Action::VIEW)
                    && $resource->setItem($item)->can(Ability::VIEW)
            )
            ->icon('eye')
            ->class('js-detail-button')
            ->showInLine();
    }
}
