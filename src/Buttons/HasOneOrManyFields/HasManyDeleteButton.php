<?php

namespace MoonShine\Buttons\HasOneOrManyFields;

use Illuminate\Database\Eloquent\Model;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Components\FormBuilder;
use MoonShine\Fields\Hidden;
use MoonShine\Fields\Relationships\HasMany;
use MoonShine\Resources\ModelResource;

final class HasManyDeleteButton
{
    public static function for(HasMany $field, ModelResource $resource, int|string $resourceItem): ActionButton
    {
        $action = static fn (Model $data): string => to_relation_route(
            'delete',
            $data->getKey(),
            $field->getRelationName(),
        );

        return ActionButton::make(
            '',
            url: $action
        )
            ->secondary()
            ->icon('heroicons.outline.trash')
            ->withConfirm(
                fields: fn(Model $item): array => [
                    Hidden::make($item->getKeyName())->setValue($item->getKey())
                ],
                method: 'DELETE',
                formBuilder: fn(FormBuilder $formBuilder, Model $item) => $formBuilder->when(
                    $field->isAsync() || $resource->isAsync(),
                    fn (FormBuilder $form): FormBuilder => $form
                        ->async(asyncEvents: 'table-updated-' . $field->getRelationName())
                )
            )
            ->canSee(
                fn (?Model $item): bool => ! is_null($item) && in_array('delete', $resource->getActiveActions())
                    && $resource->setItem($item)->can('delete')
            )
            ->showInLine();
    }
}
