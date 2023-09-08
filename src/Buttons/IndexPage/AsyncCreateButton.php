<?php

namespace MoonShine\Buttons\IndexPage;

use MoonShine\ActionButtons\ActionButton;
use MoonShine\Resources\ModelResource;

final class AsyncCreateButton
{
    public static function for(ModelResource $resource): ActionButton
    {
        return ActionButton::make(
            __('moonshine::ui.create'),
            to_page(
                $resource,
                'form-page',
                fragment: 'crud-form'
            )
        )
            ->customAttributes(['class' => 'btn btn-primary'])
            ->icon('heroicons.outline.plus')
            ->inModal(
                fn (): array|string|null => __('moonshine::ui.create'),
                fn (): string => '',
                async: true
            );
    }
}