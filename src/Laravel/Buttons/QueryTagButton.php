<?php

namespace MoonShine\Laravel\Buttons;

use MoonShine\Laravel\QueryTags\QueryTag;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\ActionButton;

final class QueryTagButton
{
    public static function for(ModelResource $resource, QueryTag $tag): ActionButton
    {
        return ActionButton::make(
            $tag->getLabel(),
            $resource->getIndexPageUrl(['query-tag' => $tag->getUri()])
        )
            ->showInLine()
            ->icon($tag->getIconValue(), $tag->isCustomIcon(), $tag->getIconPath())
            ->canSee(static fn (mixed $data): bool => $tag->isSee($data))
            ->customAttributes([
                'class' => 'query-tag-button',
                'x-data' => 'asyncLink(`btn-primary`, `' . $resource->getListEventName() . '`)',
                'x-on:disable-query-tags.window' => 'disableQueryTags',
            ])
            ->when(
                $tag->isActive(),
                static fn (ActionButton $btn): ActionButton => $btn
                    ->primary()
                    ->customAttributes([
                        'class' => 'active-query-tag',
                        'href' => $resource->getIndexPageUrl(),
                    ])
            )
            ->when(
                $resource->isAsync(),
                static fn (ActionButton $btn): ActionButton => $btn
                    ->onClick(
                        static fn ($action): string => "queryTagRequest(`{$tag->getUri()}`)",
                        'prevent'
                    )
            );
    }
}
