<?php

namespace MoonShine\Buttons\IndexPage;

use MoonShine\ActionButtons\ActionButton;
use MoonShine\Pages\Crud\FormPage;
use MoonShine\Resources\ModelResource;

final class CreateButton
{
    public static function forMode(ModelResource $resource): ActionButton
    {
        return $resource->isCreateInModal()
            ? AsyncCreateButton::for($resource)
            : CreateButton::for($resource);
    }

    public static function for(ModelResource $resource): ActionButton
    {
        return ActionButton::make(
            __('moonshine::ui.create'),
            to_page(
                page: FormPage::class,
                resource: $resource,
            )
        )
            ->primary()
            ->icon('heroicons.outline.plus');
    }
}
