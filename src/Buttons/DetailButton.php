<?php

declare(strict_types=1);

namespace MoonShine\Buttons;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Components\ActionButtons\ActionButton;
use MoonShine\Components\Modal;
use MoonShine\Resources\ModelResource;

final class DetailButton
{
    public static function for(
        ModelResource $resource,
        bool $isAsync = false
    ): ActionButton {
        if(! $resource->detailPage()) {
            return ActionButton::emptyHidden();
        }

        $action = static fn ($data): string => $resource->detailPageUrl($data);

        if($isAsync || $resource->isDetailInModal()) {
            $action = static fn ($data): string => $resource->detailPageUrl(
                $data,
                fragment: 'crud-detail'
            );
        }

        return ActionButton::make(
            '',
            $action
        )
            ->when(
                $isAsync || $resource->isDetailInModal(),
                fn (ActionButton $button): ActionButton => $button->async()->inModal(
                    title: fn (): array|string|null => __('moonshine::ui.show'),
                    content: fn (): string => '',
                    name: fn (Model $data) => "detail-modal-{$data->getKey()}",
                    builder: fn(Modal $modal) => $modal->wide()
                )
            )
            ->canSee(
                fn (?Model $item): bool => ! is_null($item) && in_array('view', $resource->getActiveActions())
                && $resource->setItem($item)->can('view')
            )
            ->icon('heroicons.outline.eye')
            ->customAttributes(['class' => 'detail-button'])
            ->showInLine();
    }
}
