<?php

namespace MoonShine\Buttons\HasOneOrManyFields;

use Illuminate\Database\Eloquent\Model;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Components\FormBuilder;
use MoonShine\Fields\Hidden;
use MoonShine\Fields\Relationships\HasMany;
use Throwable;

final class HasManyCreateButton
{
    /**
     * @throws Throwable
     */
    public static function for(HasMany $field, int $resourceId): ActionButton
    {
        $action = to_relation_route(
            'store',
            $resourceId,
        );

        $resource = $field->getResource();

        $fields = $field->hasFields()
            ? $field->getFields()->formFields()
            : $resource->getFormFields();

        return ActionButton::make(__('moonshine::ui.add'), url: $action)
            ->primary()
            ->icon('heroicons.outline.plus')
            ->canSee(
                fn (?Model $item): bool => ! is_null($item) && in_array('create', $resource->getActiveActions())
                && $resource->setItem($item)->can('create')
            )
            ->inModal(
                fn (): array|string|null => __('moonshine::ui.create'),
                fn (ActionButton $action): string => (string) FormBuilder::make($action->url())
                    ->precognitive()
                    ->name($field->getRelationName())
                    ->cast($field->getResource()->getModelCast())
                    ->fields(
                        $fields
                        ->push(Hidden::make('_relation')->setValue($field->getRelationName()))
                        ->toArray()
                    )
                    ->fill([
                        $field->getRelation()?->getForeignKeyName() => $resourceId
                    ])
            );
    }
}
