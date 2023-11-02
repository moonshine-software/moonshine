<?php

namespace MoonShine\Buttons\HasOneOrManyFields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOneOrMany;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Components\FormBuilder;
use MoonShine\Enums\PageType;
use MoonShine\Fields\Field;
use MoonShine\Fields\Fields;
use MoonShine\Fields\Hidden;
use MoonShine\Fields\Relationships\HasMany;
use MoonShine\Fields\Relationships\ModelRelationField;
use Throwable;

final class HasManyCreateButton
{
    /**
     * @throws Throwable
     */
    public static function for(HasMany $field): ActionButton
    {
        $resource = $field->getResource();
        $item = $field->getRelatedModel();

        if (! $resource->formPage()) {
            return ActionButton::emptyHidden();
        }

        $action = $resource->route('crud.store');

        $fields = $resource->getFormFields();

        $fields->onlyFields()
            ->each(fn (Field $nestedFields): Field => $nestedFields->setParent($field));

        $fields->exceptElements(
            fn (mixed $nestedFields): bool => $nestedFields instanceof ModelRelationField
                && $nestedFields->getResource() === moonshineRequest()->getResource()
        );

        $isAsync = $resource->isAsync() || $field->isAsync();

        return ActionButton::make(__('moonshine::ui.add'), url: $action)
            ->canSee(
                fn (?Model $item): bool => in_array('create', $resource->getActiveActions())
                    && $resource->can('create')
            )
            ->inModal(
                fn (): array|string|null => __('moonshine::ui.create'),
                fn (): string => (string) FormBuilder::make($action)
                    ->switchFormMode(
                        $isAsync,
                        'table-updated-' . $field->getRelationName()
                    )
                    ->name($field->getRelationName())
                    ->fillCast(
                        [$field->getRelation()?->getForeignKeyName() => $item?->getKey()],
                        $resource->getModelCast()
                    )
                    ->submit(__('moonshine::ui.save'), ['class' => 'btn-primary btn-lg'])
                    ->fields(
                        $fields
                            ->when(
                                $field->getRelation() instanceof MorphOneOrMany,
                                fn (Fields $f) => $f->push(
                                    Hidden::make($field->getRelation()?->getQualifiedMorphType())
                                        ->setValue($item::class)
                                )
                            )
                            ->push(
                                Hidden::make($field->getRelation()?->getForeignKeyName())
                                    ->setValue($item?->getKey())
                            )
                            ->push(Hidden::make('_async_field')->setValue($isAsync))
                            ->toArray()
                    )
                    ->redirect(
                        $isAsync ? null : to_page(
                            PageType::FORM->value,
                            moonshineRequest()->getResource(),
                            params: ['resourceItem' => $item?->getKey()]
                        )
                    )
            )
            ->primary()
            ->icon('heroicons.outline.plus');
    }
}
