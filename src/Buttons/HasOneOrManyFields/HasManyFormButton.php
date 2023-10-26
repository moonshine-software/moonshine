<?php

declare(strict_types=1);

namespace MoonShine\Buttons\HasOneOrManyFields;

use Illuminate\Database\Eloquent\Model;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Buttons\IndexPage\FormButton;
use MoonShine\Fields\Relationships\HasMany;
use MoonShine\Resources\ModelResource;

final class HasManyFormButton
{
    public static function forMode(ModelResource $resource, HasMany $field): ActionButton
    {
        if(! $resource->formPage()) {
            return ActionButton::emptyHidden();
        }

        return ($resource->isEditInModal() || $field->isAsync())
            ? HasManyFormButton::for($resource, $field->getRelationName())
            : FormButton::for($resource);
    }

    public static function for(ModelResource $resource, string $tableName = 'default'): static
    {
        return ActionButton::make(
            '',
            url: static fn ($data): string => to_page(
                page: $resource->formPage(),
                resource: $resource,
                params: [
                    'resourceItem' => $data->getKey(),
                    '_tableName' => $tableName,
                    '_asyncMode' => true,
                ],
                fragment: 'crud-form'
            )
        )
            ->primary()
            ->icon('heroicons.outline.pencil')
            ->inModal(
                fn (): array|string|null => __('moonshine::ui.edit'),
                fn (): string => '',
                async: true
            )
            ->canSee(
                fn (?Model $item): bool => ! is_null($item) && in_array('update', $resource->getActiveActions())
                    && $resource->setItem($item)->can('update')
            )
            ->customAttributes(['class' => 'edit-button'])
            ->showInLine()
        ;
    }
}
