<?php

declare(strict_types=1);

namespace MoonShine\Buttons;

use Illuminate\Database\Eloquent\Model;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Components\FormBuilder;
use MoonShine\Enums\JsEvent;
use MoonShine\Fields\Field;
use MoonShine\Fields\Hidden;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Fields\Relationships\BelongsToMany;
use MoonShine\Resources\ModelResource;
use MoonShine\Support\AlpineJs;
use Throwable;

final class BelongsToOrManyButton
{
    /**
     * @throws Throwable
     */
    public static function for(
        BelongsToMany|BelongsTo $field,
        ?ActionButton $button = null
    ): ActionButton {
        /** @var ModelResource $resource */
        $resource = $field->getResource();

        if (! $resource->formPage()) {
            return ActionButton::emptyHidden();
        }

        $action = $resource->route('crud.store');

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
                    ->reactiveUrl(
                        moonshineRouter()->reactive($data?->getKey(), $resource->formPage(), $resource)
                    )
                    ->switchFormMode(
                        true,
                        [
                            AlpineJs::event(JsEvent::FRAGMENT_UPDATED, $field->getRelationName()),
                            AlpineJs::event(JsEvent::FORM_RESET, $resource->uriKey()),
                        ]
                    )
                    ->name($resource->uriKey())
                    ->fillCast(
                        [],
                        $resource->getModelCast()
                    )
                    ->submit(__('moonshine::ui.save'), ['class' => 'btn-primary btn-lg'])
                    ->fields($getFields),
                wide: true,
                closeOutside: false,
            )
            ->primary()
            ->icon('heroicons.outline.plus');
    }
}
