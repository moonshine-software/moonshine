<?php

declare(strict_types=1);

namespace MoonShine\Buttons\IndexPage;

use MoonShine\ActionButtons\ActionButton;
use MoonShine\Resources\ModelResource;

final class AsyncEditButton
{
    public static function for(ModelResource $resource): ActionButton
    {
        return ActionButton::make(
            '',
            url: fn ($data): string => to_page(
                $resource,
                'form-page',
                ['resourceItem' => $data->getKey()],
                fragment: 'crud-form'
            )
        )
            ->customAttributes(['class' => 'btn btn-primary'])
            ->icon('heroicons.outline.pencil')
            ->inModal(
                fn (): array|string|null => __('moonshine::ui.create'),
                fn (): string => '',
                async: true
            )
            ->showInLine()
        ;
    }
}