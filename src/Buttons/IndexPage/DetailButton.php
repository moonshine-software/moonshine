<?php

declare(strict_types=1);

namespace MoonShine\Buttons\IndexPage;

use Illuminate\Database\Eloquent\Model;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Resources\ModelResource;

final class DetailButton
{
    public static function forMode(ModelResource $resource): ActionButton
    {
        if(! $resource->detailPage()) {
            return ActionButton::emptyHidden();
        }

        return $resource->isDetailInModal()
            ? AsyncDetailButton::for($resource)
            : DetailButton::for($resource);
    }

    public static function for(ModelResource $resource): ActionButton
    {
        return ActionButton::make(
            '',
            url: static fn ($data): string => to_page(
                page: $resource->detailPage(),
                resource: $resource,
                params: ['resourceItem' => $data->getKey()]
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
