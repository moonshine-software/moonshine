<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Buttons;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Laravel\Fields\Relationships\BelongsToMany;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Modal;
use MoonShine\UI\Fields\Field;
use MoonShine\UI\Fields\Hidden;
use Throwable;

final class BelongsToManyButton
{
    /**
     * @throws Throwable
     */
    public static function for(
        BelongsToMany $field,
        ?ActionButton $button = null
    ): ActionButton {
        /** @var ModelResource $resource */
        $resource = $field->getResource();

        if (! $resource->getFormPage()) {
            return ActionButton::emptyHidden();
        }

        $action = $resource->getRoute('crud.store');

        $getFields = function () use ($resource, $field) {
            $fields = $resource->getFormFields();

            $fields->onlyFields()
                ->each(fn (Field $nestedFields): Field => $nestedFields->setParent($field));

            return $fields
                ->push(Hidden::make('_async_field')->setValue(true))
                ->toArray();
        };

        $actionButton = $button
            ? $button->setUrl($action)
            : ActionButton::make(__('moonshine::ui.add'), url: $action);

        return $actionButton
            ->canSee(fn (): bool => in_array('create', $resource->getActiveActions()) && $resource->can('create'))
            ->inModal(
                title: fn (): array|string|null => __('moonshine::ui.create'),
                content: fn (?Model $data): string => (string) FormBuilder::make($action)
                    ->switchFormMode(
                        true,
                        [
                            AlpineJs::event(JsEvent::FRAGMENT_UPDATED, $field->getRelationName()),
                            AlpineJs::event(JsEvent::FORM_RESET, $resource->getUriKey()),
                        ]
                    )
                    ->name($resource->getUriKey())
                    ->fillCast(
                        [],
                        $resource->getModelCast()
                    )
                    ->submit(__('moonshine::ui.save'), ['class' => 'btn-primary btn-lg'])
                    ->fields($getFields),
                name: "modal-belongs-to-many-{$field->getRelationName()}",
                builder: fn (Modal $modal): Modal => $modal->wide()->closeOutside(false)
            )
            ->primary()
            ->icon('plus');
    }
}
