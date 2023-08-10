<?php

namespace MoonShine\Buttons\ShowPage;

use MoonShine\ActionButtons\ActionButton;
use MoonShine\Resources\ModelResource;

final class FormButton
{
    public static function for(ModelResource $resource): ActionButton
    {
        return ActionButton::make(
            '',
            url: fn (): string => route('moonshine.page', [
                'resourceUri' => $resource->uriKey(),
                'pageUri' => 'form-page',
                'resourceItem' => request('resourceItem'),
            ])
        )
            ->canSee(fn (): bool => $resource->can('update'))
            ->customAttributes(['class' => 'btn-purple'])
            ->icon('heroicons.outline.pencil')
            ->showInLine();
    }
}
