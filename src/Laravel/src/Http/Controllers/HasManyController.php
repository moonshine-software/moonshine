<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOneOrMany;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\Contracts\UI\TableBuilderContract;
use MoonShine\Laravel\Collections\Fields;
use MoonShine\Laravel\Exceptions\ModelRelationFieldException;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\Laravel\Fields\Relationships\ModelRelationField;
use MoonShine\Laravel\Fields\Relationships\MorphMany;
use MoonShine\Laravel\Http\Requests\Relations\RelationModelFieldRequest;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Fields\Hidden;
use Throwable;

final class HasManyController extends MoonShineController
{
    /**
     * @throws Throwable
     */
    public function formComponent(RelationModelFieldRequest $request): string
    {
        $parent = $request->getResource()?->getItemOrInstance();

        /** @var null|HasMany|MorphMany $field */
        $field = $request->getPageField();

        if ($field === null) {
            throw ModelRelationFieldException::notFound();
        }

        /** @var ModelResource $resource */
        $resource = $field->getResource();

        $item = $resource
            ->stopGettingItemFromUrl()
            ->setItemID($request->input('_key', false))
            ->getItemOrInstance();

        $update = $item->getKey() !== null;
        $relation = $parent?->{$field->getRelationName()}();

        $field->fillCast($parent, $request->getResource()?->getCaster());

        $action = $update
            ? static fn (Model $data) => $resource->getRoute('crud.update', $data->getKey())
            : static fn (?Model $data) => $resource->getRoute('crud.store');

        $isAsync = $field->isAsync();

        $getFields = static function () use ($resource, $field, $parent, $update): array {
            $fields = $resource->getFormFields();

            $fields->onlyFields()->each(
                static fn (FieldContract $nestedFields): FieldContract => $nestedFields
                    ->setParent($field)
            );

            $relation = $field->getRelation();

            return $fields->when(
                $relation instanceof MorphOneOrMany,
                static fn (Fields $f) => $f->push(
                    /** @phpstan-ignore-next-line  */
                    Hidden::make($relation?->getMorphType())->setValue($parent::class)
                )
            )->when(
                $update,
                static fn (Fields $f) => $f->push(
                    Hidden::make('_method')->setValue('PUT'),
                )
            )
                ->push(
                    Hidden::make($relation?->getForeignKeyName())
                        ->setValue($parent->getKey())
                )
                ->toArray();
        };

        $formName = "{$resource->getUriKey()}-unique-" . ($item->getKey() ?? "create");
        $redirectRoute = $field->getRedirectAfter($parent);

        return (string) FormBuilder::make($action($item))
            /** @phpstan-ignore-next-line  */
            ->fields($getFields)
            ->reactiveUrl(
                static fn (): string => moonshineRouter()
                    ->getEndpoints()
                    ->reactive(page: $resource->getFormPage(), resource: $resource, extra: ['key' => $item->getKey()])
            )
            ->name($formName)
            ->switchFormMode(
                $isAsync,
                array_filter([
                    $resource->getListEventName($field->getRelationName()),
                    $update ? null : AlpineJs::event(JsEvent::FORM_RESET, $formName),
                ])
            )
            ->when(
                $update,
                static fn (FormBuilderContract $form): FormBuilderContract => $form->fillCast(
                    $item,
                    $resource->getCaster()
                ),
                static fn (FormBuilderContract $form): FormBuilderContract => $form->fillCast(
                    array_filter([
                        $relation?->getForeignKeyName() => $parent?->getKey(),
                        ...$relation instanceof MorphOneOrMany
                            ? [$relation->getMorphType() => $parent?->getMorphClass()]
                            : [],
                    ], static fn ($value) => filled($value)),
                    $resource->getCaster()
                )
            )
            ->submit(__('moonshine::ui.save'), ['class' => 'btn-primary'])
            ->onBeforeFieldsRender(static fn (FieldsContract $fields): FieldsContract => $fields->exceptElements(
                static fn (ComponentContract $element): bool => $element instanceof ModelRelationField
                    && $element->isToOne()
                    && $element->getColumn() === $relation->getForeignKeyName()
            ))
            ->buttons($field->getFormButtons())
            ->redirect($redirectRoute)
            ->when(
                ! $update && \is_null($redirectRoute),
                static fn (FormBuilderContract $form): FormBuilderContract => $form->withoutRedirect(),
            );
    }

    /**
     * @throws Throwable
     */
    public function listComponent(RelationModelFieldRequest $request): string
    {
        /* @var \MoonShine\Laravel\Resources\ModelResource $parentResource */
        $parentResource = $request->getResource();

        $parentResource->setQueryParams(
            $request->only($parentResource->getQueryParamsKeys())
        );

        $parentItem = $parentResource->getItemOrInstance();

        /**
         * @var ?HasMany $field
         */
        $field = $request->getPageField();

        if ($field === null) {
            throw ModelRelationFieldException::notFound();
        }

        $field->fillCast(
            $parentItem,
            $parentResource->getCaster()
        );

        $value = $field->getComponent();

        if ($value instanceof TableBuilderContract && $request->filled('_key')) {
            return (string) $this->responseWithTable($value, $field->getResource());
        }

        return (string) $value->render();
    }
}
