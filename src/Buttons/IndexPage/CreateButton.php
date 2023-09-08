<?php

namespace MoonShine\Buttons\IndexPage;

use MoonShine\ActionButtons\ActionButton;
use MoonShine\Resources\ModelResource;

final class CreateButton
{
    public static function for(ModelResource $resource): ActionButton
    {
        return ActionButton::make(__('moonshine::ui.create'), to_page($resource, 'form-page'))
            ->customAttributes(['class' => 'btn btn-primary'])
            ->icon('heroicons.outline.plus');
    }
}