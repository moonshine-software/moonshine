<?php

declare(strict_types=1);

namespace MoonShine\Buttons;

use Illuminate\Database\Eloquent\Model;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Resources\ModelResource;

final class DetailButton
{
    public static function for(
        ModelResource $resource
    ): ActionButton {
        if (! $resource->detailPage()) {
            return ActionButton::emptyHidden();
        }

        $action = static fn ($data): string => $resource->detailPageUrl($data);

        if ($resource->isDetailInModal()) {
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
                $resource->isDetailInModal(),
                fn (ActionButton $button): ActionButton => $button->inModal(
                    title: fn (): array|string|null => __('moonshine::ui.show'),
                    content: fn (): string => '',
                    async: true,
                    wide: true,
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
