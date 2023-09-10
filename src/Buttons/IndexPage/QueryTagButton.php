<?php

namespace MoonShine\Buttons\IndexPage;

use MoonShine\ActionButtons\ActionButton;
use MoonShine\Pages\Crud\IndexPage;
use MoonShine\QueryTags\QueryTag;
use MoonShine\Resources\ModelResource;

final class QueryTagButton
{
    public static function for(ModelResource $resource, QueryTag $tag): ActionButton
    {
        return ActionButton::make(
            $tag->label(),
            to_page($resource, IndexPage::class, params: ['query-tag' => $tag->uri()])
        )
            ->showInLine()
            ->icon($tag->iconValue())
            ->canSee(fn () => $tag->isSee(moonshineRequest()))
            ->when(
                $tag->isActive(),
                fn (ActionButton $btn): ActionButton => $btn->customAttributes([
                    'class' => 'btn-primary',
                ])
            );
    }
}