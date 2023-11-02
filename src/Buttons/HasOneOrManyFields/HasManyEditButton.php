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

final class HasManyEditButton
{
    /**
     * @throws Throwable
     */
    public static function for(HasMany $field): ActionButton
    {
        $resource = $field->getResource();
        $parent = $field->getRelatedModel();

        if (! $resource->formPage()) {
            return ActionButton::emptyHidden();
        }

        $action = static fn (Model $data) => $resource
            ->route('crud.update', $data->getKey());

        $fields = $resource->getFormFields();

        $fields->onlyFields()
            ->each(fn (Field $nestedFields): Field => $nestedFields->setParent($field));

        $fields->exceptElements(
            fn (mixed $nestedFields): bool => $nestedFields instanceof ModelRelationField
                && $nestedFields->getResource() === moonshineRequest()->getResource()
        );

        $isAsync = $resource->isAsync() || $field->isAsync();

        return ActionButton::make('', url: $action)
            ->canSee(
                fn (?Model $item): bool => ! is_null($item) && in_array('update', $resource->getActiveActions())
                    && $resource->setItem($item)->can('update')
            )
            ->inModal(
                fn (): array|string|null => __('moonshine::ui.edit'),
                fn (Model $data): string => (string) FormBuilder::make($action($data))
                    ->switchFormMode(
                        $isAsync,
                        'table-updated-' . $field->getRelationName()
                    )
                    ->name($field->getRelationName())
                    ->fillCast(
                        $data,
                        $resource->getModelCast()
                    )
                    ->submit(__('moonshine::ui.save'), ['class' => 'btn-primary btn-lg'])
                    ->fields(
                        $fields
                            ->when(
                                $field->getRelation() instanceof MorphOneOrMany,
                                fn (Fields $f) => $f->push(
                                    Hidden::make($field->getRelation()?->getQualifiedMorphType())
                                        ->setValue($parent::class)
                                )
                            )
                            ->push(
                                Hidden::make('_method')->setValue('PUT'),
                            )
                            ->push(
                                Hidden::make($field->getRelation()?->getForeignKeyName())
                                    ->setValue($parent->getKey())
                            )
                            ->push(Hidden::make('_async_field')->setValue($isAsync))
                            ->toArray()
                    )
                    ->redirect(
                        $isAsync ? null : to_page(
                            PageType::FORM->value,
                            moonshineRequest()->getResource(),
                            params: ['resourceItem' => $parent->getKey()]
                        )
                    )
            )
            ->primary()
            ->icon('heroicons.outline.pencil');
    }
}
