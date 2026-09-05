<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Buttons;

use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Fields\Relationships\BelongsToMany;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\Ability;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Modal;
use MoonShine\UI\Fields\Hidden;
use Throwable;

final class BelongsToOrManyButton
{
    /**
     * @throws Throwable
     */
    public static function for(
        BelongsToMany|BelongsTo $field,
        ?ActionButtonContract $button = null
    ): ActionButtonContract {
        /** @var ModelResource $resource */
        $resource = $field->getResourceOrFail();

        if (! $resource->getFormPage()) {
            return ActionButton::emptyHidden();
        }

        $action = $resource->getRoute('crud.store');

        $getFields = static function () use ($resource, $field): array {
            $fields = $resource->getFormFields();

            $fields->onlyFields()
                ->each(static fn (FieldContract $nestedFields): FieldContract => $nestedFields->setParent($field));

            $fields->push(
                Hidden::make('_relation_name')->setValue($field->getRelationName())
            );

            return $fields->all();
        };

        $actionButton = $button instanceof ActionButtonContract
            ? $button->setUrl($action)
            : ActionButton::make(__('moonshine::ui.add'), url: $action);

        return $actionButton
            ->name("belongs-to-many-{$field->getRelationName()}-button")
            ->canSee(static fn (): bool => $resource->hasAction(Action::CREATE) && $resource->can(Ability::CREATE))
            ->inModal(
                title: static fn (): array|string => __('moonshine::ui.create'),
                content: static fn (mixed $data): string => (string) FormBuilder::make($action)
                    ->withoutRedirect()
                    ->reactiveUrl(
                        $field->getCore()->getRouter()->getEndpoints()->reactive($resource->getFormPage(), $resource)
                    )
                    ->async(events: [
                        AlpineJs::event(JsEvent::FRAGMENT_UPDATED, $field->getRelationName()),
                        AlpineJs::event(JsEvent::FORM_RESET, $resource->getUriKey()),
                    ])
                    ->name($resource->getUriKey())
                    ->class("form-resource-{$resource->getUriKey()}-{$field->getRelationName()}")
                    ->fillCast(
                        [],
                        $resource->getCaster()
                    )
                    ->submit(__('moonshine::ui.save'), ['class' => 'btn-primary'])
                    ->fields($getFields),
                name: static fn (mixed $data): string => "modal-belongs-to-or-many-{$field->getResourceOrFail()->getUriKey()}-{$field->getRelationName()}",
                builder: static fn (Modal $modal): Modal => $modal->wide()->closeOutside(false)
            )
            ->primary()
            ->icon('plus');
    }
}
