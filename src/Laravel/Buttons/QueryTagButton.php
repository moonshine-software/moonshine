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
            ])
            ->when(
                $tag->isActive(),
                static fn (ActionButton $btn): ActionButton => $btn
                    ->primary()
                    ->customAttributes([
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
            )
            ->when(
                $tag->isDefault(),
                fn (ActionButton $btn): ActionButton => $btn
                    ->customAttributes([
                        'class' => 'query-tag-default',
                    ])
            );
    }
}
