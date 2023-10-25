<?php

namespace MoonShine\Buttons\HasOneOrManyFields;

use Illuminate\Database\Eloquent\Model;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Components\FormBuilder;
use MoonShine\Fields\Field;
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
        $resource = $field->getResource();

        if(!$resource->formPage()) {
            return ActionButton::emptyButton();
        }

        $action = to_relation_route(
            'store',
            $resourceId,
        );

        $resource = $field->getResource();

        $fields = $field->hasFields()
            ? $field->getFields()->formFields()
            : $resource->getFormFields();

        $fields->onlyFields()->each(fn (Field $nestedFields): Field => $nestedFields->setParent($field));

        return ActionButton::make(__('moonshine::ui.add'), url: $action)
            ->primary()
            ->icon('heroicons.outline.plus')
            ->canSee(
                fn (?Model $item): bool => in_array('create', $resource->getActiveActions())
                && $resource->can('create')
            )
            ->inModal(
                fn (): array|string|null => __('moonshine::ui.create'),
                fn (ActionButton $action): string => (string) FormBuilder::make($action->url())
                    ->switchFormMode($resource->isAsync(), 'table-updated-' . $field->getRelationName())
                    ->name($field->getRelationName())
                    ->fillCast(
                        [$field->getRelation()?->getForeignKeyName() => $resourceId],
                        $field->getResource()->getModelCast()
                    )
                    ->fields(
                        $fields
                        ->push(Hidden::make('_relation')->setValue($field->getRelationName()))
                        ->toArray()
                    )
            );
    }
}
