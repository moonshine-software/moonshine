<?php

declare(strict_types=1);

namespace MoonShine\Buttons\IndexPage;

use Illuminate\Database\Eloquent\Model;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Resources\ModelResource;

final class DetailButton
{
    public static function for(ModelResource $resource, bool $isAsync = false): ActionButton
    {
        if(! $resource->detailPage()) {
            return ActionButton::emptyHidden();
        }

        $action = static fn ($data): string => to_page(
            page: $resource->detailPage(),
            resource: $resource,
            params: ['resourceItem' => $data->getKey()]
        );

        if($isAsync || $resource->isDetailInModal()) {
            $action = static fn ($data): string => to_page(
                page: $resource->detailPage(),
                resource: $resource,
                params: ['resourceItem' => $data->getKey()],
                fragment: 'crud-show-table'
            );
        }

        return ActionButton::make(
            '',
            $action
        )
            ->when(
                $isAsync || $resource->isDetailInModal(),
                fn(ActionButton $button) => $button->inModal(
                    fn (): array|string|null => __('moonshine::ui.show'),
                    fn (): string => '',
                    async: true
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
