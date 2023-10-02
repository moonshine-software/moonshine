<?php

namespace MoonShine\Buttons\DetailPage;

use MoonShine\ActionButtons\ActionButton;
use MoonShine\Pages\Crud\FormPage;
use MoonShine\Resources\ModelResource;

final class FormButton
{
    public static function for(ModelResource $resource): ActionButton
    {
        return ActionButton::make(
            '',
            url: static fn (): string => to_page(
                page: FormPage::class,
                resource: $resource,
                params: ['resourceItem' => request('resourceItem')]
            )
        )
            ->primary()
            ->icon('heroicons.outline.pencil')
            ->showInLine();
    }
}
