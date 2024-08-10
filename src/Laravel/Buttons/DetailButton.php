<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Buttons;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Laravel\Enums\Ability;
use MoonShine\Laravel\Enums\Action;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Modal;

final class DetailButton
{
    public static function for(
        ModelResource $resource,
        string $modalName = 'detail-modal',
    ): ActionButtonContract {
        if(! $resource->getDetailPage()) {
            return ActionButton::emptyHidden();
        }

        $action = static fn ($data): string => $resource->getDetailPageUrl($data);

        if($resource->isDetailInModal()) {
            $action = static fn ($data): string => $resource->getDetailPageUrl(
                $data,
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
                    name: static fn (Model $data): string => "$modalName-{$data->getKey()}",
                    builder: static fn (Modal $modal): Modal => $modal->wide()
                )
            )
            ->canSee(
                static fn (?Model $item): bool => ! is_null($item) && $item->exists
                    && $resource->hasAction(Action::VIEW)
                    && $resource->setItem($item)->can(Ability::VIEW)
            )
            ->icon('eye')
            ->class('js-detail-button')
            ->showInLine();
    }
}
