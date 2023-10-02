<?php

declare(strict_types=1);

namespace MoonShine\Buttons\IndexPage;

use MoonShine\ActionButtons\ActionButton;
use MoonShine\Pages\Crud\FormPage;
use MoonShine\Resources\ModelResource;

final class AsyncEditButton
{
    public static function for(ModelResource $resource): ActionButton
    {
        return ActionButton::make(
            '',
            url: static fn ($data): string => to_page(
                page: FormPage::class,
                resource: $resource,
                params: ['resourceItem' => $data->getKey()],
                fragment: 'crud-form'
            )
        )
            ->primary()
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
