<?php

declare(strict_types=1);

namespace MoonShine\Buttons\IndexPage;

use MoonShine\ActionButtons\ActionButton;
use MoonShine\Resources\ModelResource;

final class FormButton
{
    public static function for(ModelResource $resource): ActionButton
    {
        return ActionButton::make(
            '',
            url: fn ($data): string => to_page(
                $resource,
                'form-page',
                ['resourceItem' => $data->getKey(), '_fragment-load' => 'form']
            )
        )
            ->customAttributes(['class' => 'btn-purple'])
            ->icon('heroicons.outline.pencil')
            ->inModal(fn(): string => '', fn(): string => '', async: true)
            ->showInLine();
    }
}
