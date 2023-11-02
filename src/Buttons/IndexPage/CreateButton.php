<?php

namespace MoonShine\Buttons\IndexPage;

use Illuminate\Database\Eloquent\Model;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Resources\ModelResource;

final class CreateButton
{
    public static function for(
        ModelResource $resource,
        string $tableName = 'default',
        bool $isAsync = false,
    ): ActionButton
    {
        if(! $resource->formPage()) {
            return ActionButton::emptyHidden();
        }

        $action = to_page(
            page: $resource->formPage(),
            resource: $resource,
        );

        if($isAsync || $resource->isCreateInModal()) {
            $action = to_page(
                page: $resource->formPage(),
                resource: $resource,
                params: [
                    '_tableName' => $tableName,
                    '_async_form' => $isAsync,
                ],
                fragment: 'crud-form'
            );
        }

        return ActionButton::make(
            __('moonshine::ui.create'),
            $action
        )
            ->when(
                $isAsync || $resource->isCreateInModal(),
                fn(ActionButton $button): ActionButton => $button->inModal(
                    fn (): array|string|null => __('moonshine::ui.create'),
                    fn (): string => '',
                    async: true
                )
            )
            ->canSee(
                fn (?Model $item): bool => in_array('create', $resource->getActiveActions())
                && $resource->can('create')
            )
            ->primary()
            ->icon('heroicons.outline.plus');
    }
}
