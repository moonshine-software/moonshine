<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Buttons;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Modal;

final class DetailButton
{
    public static function for(
        ModelResource $resource,
        bool $isAsync = true
    ): ActionButton {
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
            ->when(
                $resource->isDetailInModal(),
                fn (ActionButton $button): ActionButton => $button->async()->inModal(
                    title: fn (): array|string|null => __('moonshine::ui.show'),
                    content: fn (): string => '',
                    name: fn (Model $data): string => "detail-modal-{$data->getKey()}",
                    builder: fn (Modal $modal): Modal => $modal->wide()
                )
            )
            ->canSee(
                fn (?Model $item): bool => ! is_null($item) && in_array('view', $resource->getActiveActions())
                && $resource->setItem($item)->can('view')
            )
            ->icon('eye')
            ->customAttributes(['class' => 'detail-button'])
            ->showInLine();
    }
}
