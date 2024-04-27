<?php

namespace MoonShine\Buttons;

use MoonShine\Components\ActionButtons\ActionButton;
use MoonShine\QueryTags\QueryTag;
use MoonShine\Resources\ModelResource;

final class QueryTagButton
{
    public static function for(ModelResource $resource, QueryTag $tag): ActionButton
    {
        return ActionButton::make(
            $tag->getLabel(),
            $resource->indexPageUrl(['query-tag' => $tag->uri()])
        )
            ->showInLine()
            ->icon($tag->iconValue())
            ->canSee(fn (mixed $data): bool => $tag->isSee($data))
            ->customAttributes([
                'class' => 'query-tag-button',
                'x-data' => 'asyncLink(`btn-primary`, `' . $resource->listEventName() . '`)',
                'x-on:disable-query-tags.window' => 'disableQueryTags',
            ])
            ->when(
                $tag->isActive(),
                fn (ActionButton $btn): ActionButton => $btn
                    ->primary()
                    ->customAttributes([
                        'class' => 'active-query-tag',
                        'href' => $resource->indexPageUrl(),
                    ])
            )
            ->when(
                $resource->isAsync(),
                fn (ActionButton $btn): ActionButton => $btn
                    ->onClick(
                        fn ($action): string => "queryTagRequest(`{$tag->uri()}`)",
                        'prevent'
                    )
            );
    }
}
