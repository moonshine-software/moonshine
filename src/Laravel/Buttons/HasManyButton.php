<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Buttons;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOneOrMany;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\Laravel\Fields\Relationships\ModelRelationField;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\UI\Collections\Fields;
use MoonShine\UI\Collections\MoonShineRenderElements;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Modal;
use MoonShine\UI\Fields\Field;
use MoonShine\UI\Fields\Hidden;
use Throwable;

final class HasManyButton
{
    /**
     * @throws Throwable
     */
    public static function for(
        HasMany $field,
        bool $update = false,
        ?ActionButton $button = null
    ): ActionButton {
        /** @var ModelResource $resource */
        $resource = $field->getResource();
        $parent = $field->getRelatedModel();
        $relation = $parent?->{$field->getRelationName()}();

        if (! $resource->formPage()) {
            return ActionButton::emptyHidden();
        }

        $action = $update
            ? static fn (Model $data) => $resource->route('crud.update', $data->getKey())
            : static fn (?Model $data) => $resource->route('crud.store');

        $isAsync = $resource->isAsync() || $field->isAsync();

        $getFields = function () use ($resource, $field, $isAsync, $parent, $update) {
            $fields = $resource->getFormFields();

            $fields->onlyFields()
                ->each(fn (Field $nestedFields): Field => $nestedFields->setParent($field))
                // Uncomment if you need a parent resource
                //->onlyRelationFields()
                //->each(fn (ModelRelationField $nestedFields): Field => $nestedFields->setParentResource($resource))
            ;

            return $fields->when(
                $field->getRelation() instanceof MorphOneOrMany,
                fn (Fields $f) => $f->push(
                    Hidden::make($field->getRelation()?->getMorphType())
                        ->setValue($parent::class)
                )
            )->when(
                $update,
                fn (Fields $f) => $f->push(
                    Hidden::make('_method')->setValue('PUT'),
                )
            )
                ->push(
                    Hidden::make($field->getRelation()?->getForeignKeyName())
                        ->setValue($parent->getKey())
                )
                ->push(Hidden::make('_async_field')->setValue($isAsync))
                ->toArray();
        };

        $authorize = $update
            ? fn (?Model $item): bool => ! is_null($item) && in_array('update', $resource->getActiveActions())
            && $resource->setItem($item)->can('update')
            : fn (?Model $item): bool => in_array('create', $resource->getActiveActions())
            && $resource->can('create');

        $actionButton = $button
            ? $button->setUrl($action)
            : ActionButton::make($update ? '' : __('moonshine::ui.add'), url: $action);

        return $actionButton
            ->canSee($authorize)
            ->inModal(
                title: fn (): array|string|null => __($update ? 'moonshine::ui.edit' : 'moonshine::ui.create'),
                content: fn (?Model $data): string => (string) FormBuilder::make($action($data))
                    ->reactiveUrl(
                        fn (): string => moonshineRouter()
                            ->reactive(key: $data?->getKey(), page: $resource->formPage(), resource: $resource)
                    )
                    ->name($resource->uriKey())
                    ->switchFormMode(
                        $isAsync,
                        [
                            $resource->listEventName($field->getRelationName()),
                            AlpineJs::event(JsEvent::FORM_RESET, $resource->uriKey()),
                        ]
                    )
                    ->when(
                        $update,
                        fn (FormBuilder $form): FormBuilder => $form->fillCast(
                            $data,
                            $resource->getModelCast()
                        ),
                        fn (FormBuilder $form): FormBuilder => $form->fillCast(
                            array_filter([
                                $field->getRelation()?->getForeignKeyName() => $parent?->getKey(),
                                ...$field->getRelation() instanceof MorphOneOrMany
                                    ? [$field->getRelation()?->getMorphType() => $parent?->getMorphClass()]
                                    : [],
                            ], static fn ($value) => filled($value)),
                            $resource->getModelCast()
                        )
                    )
                    ->submit(__('moonshine::ui.save'), ['class' => 'btn-primary btn-lg'])
                    ->fields($getFields)
                    ->onBeforeFieldsRender(fn (Fields $fields): MoonShineRenderElements => $fields->exceptElements(
                        fn (mixed $field): bool => $field instanceof ModelRelationField
                            && $field->toOne()
                            && $field->getColumn() === $relation->getForeignKeyName()
                    ))
                    ->buttons($resource->getFormButtons())
                    ->redirect(
                        $isAsync ?
                            null
                            : moonshineRequest()
                                ->getResource()
                                ?->formPageUrl($parent)
                    ),
                name: fn (?Model $data): string => "modal-has-many-{$field->getRelationName()}-" . ($update ? $data->getKey() : 'create'),
                builder: fn (Modal $modal): Modal => $modal->wide()->closeOutside(false)
            )
            ->primary()
            ->icon($update ? 'pencil' : 'plus');
    }
}
