<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Buttons;

use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Laravel\QueryTags\QueryTag;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\ActionButton;

final class QueryTagButton
{
    public static function for(ModelResource $resource, QueryTag $tag): ActionButtonContract
    {
        return ActionButton::make(
            $tag->getLabel(),
            $resource->getIndexPageUrl(['query-tag' => $tag->getUri()])
        )
            ->name("query-tag-{$tag->getUri()}-button")
            ->showInLine()
            ->icon($tag->getIconValue(), $tag->isCustomIcon(), $tag->getIconPath())
            ->canSee(static fn (mixed $data): bool => $tag->isSee($data))
            ->class('js-query-tag-button')
            ->xDataMethod('asyncLink', 'btn-primary', $resource->getListEventName())
            ->when(
                $tag->isActive(),
                static fn (ActionButtonContract $btn): ActionButtonContract => $btn
                    ->primary()
                    ->customAttributes([
                        'href' => $resource->getIndexPageUrl(),
                    ])
            )
            ->when(
                $resource->isAsync(),
                static fn (ActionButtonContract $btn): ActionButtonContract => $btn
                    ->onClick(
                        static fn ($action): string => "queryTagRequest(`{$tag->getUri()}`)",
                        'prevent'
                    )
            )
            ->when(
                $tag->isDefault(),
                static fn (ActionButtonContract $btn): ActionButtonContract => $btn->class('js-query-tag-default')
            );
    }
}
