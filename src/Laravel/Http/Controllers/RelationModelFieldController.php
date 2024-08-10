<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Controllers;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOneOrMany;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\Contracts\UI\TableBuilderContract;
use MoonShine\Core\Collections\Renderables;
use MoonShine\Laravel\Collections\Fields;
use MoonShine\Laravel\Contracts\Fields\HasAsyncSearchContract;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\Laravel\Fields\Relationships\ModelRelationField;
use MoonShine\Laravel\Fields\Relationships\MorphTo;
use MoonShine\Laravel\Http\Requests\Relations\RelationModelFieldRequest;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Laravel\Support\DBOperators;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Fields\Hidden;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class RelationModelFieldController extends MoonShineController
{
    /**
     * @throws Throwable
     */
    public function search(RelationModelFieldRequest $request): Response
    {
        $field = $request->getPageField();

        if (! $field instanceof HasAsyncSearchContract) {
            return response()->json();
        }

        /* @var \MoonShine\Laravel\Resources\ModelResource $resource */
        $resource = $field->getResource();

        $model = $resource->getModel();

        $searchColumn = $field->getAsyncSearchColumn() ?? $resource->getColumn();

        if ($field instanceof MorphTo) {
            $field->fillCast([], $resource->getModelCast());

            $morphClass = $field->getWrapName()
                ? data_get($request->input($field->getWrapName(), []), $field->getMorphType())
                : $request->input($field->getMorphType());

            $model = new $morphClass();
            $searchColumn = $field->getSearchColumn($morphClass);
        }

        $query = $resource->resolveQuery();
        $term = $request->input('query');

        if (! is_null($field->getAsyncSearchQuery())) {
            $query = value(
                $field->getAsyncSearchQuery(),
                $query,
                $term,
                $request,
                $field
            );
        }

        $values = $request->input($field->getColumn(), '') ?? '';

        $except = is_array($values)
            ? array_keys($values)
            : array_filter(explode(',', (string) $values));

        $offset = $request->input('offset', 0);

        $query->when(
            $term && is_null($field->getAsyncSearchQuery()),
            static fn (Builder $q) => $q->where(
                $searchColumn,
                DBOperators::byModel($q->getModel())->like(),
                "%$term%"
            )
        )
            ->whereNotIn($model->getKeyName(), $except)
            ->offset($offset)
            ->limit($field->getAsyncSearchCount());

        return response()->json(
            $query->get()->map(
                static fn ($model): array => $field->getAsyncSearchOption($model, $searchColumn)->toArray()
            )->toArray()
        );
    }

    /**
     * @throws Throwable
     */
    public function searchRelations(RelationModelFieldRequest $request): mixed
    {
        /* @var \MoonShine\Laravel\Resources\ModelResource $parentResource */
        $parentResource = $request->getResource();

        $parentResource->setQueryParams(
            $request->only($parentResource->getQueryParamsKeys())
        );

        $parentItem = $parentResource->getItemOrInstance();

        $field = $request->getField();

        $field?->fillCast(
            $parentItem,
            $parentResource->getModelCast()
        );

        $value = $field?->getComponent();

        if ($value instanceof TableBuilderContract && $request->filled('_key')) {
            return $this->responseWithTable($value);
        }

        return $value->render();
    }

    /**
     * @throws Throwable
     */
    public function hasManyForm(RelationModelFieldRequest $request): string
    {
        $parent = $request->getResource()?->getItemOrInstance();

        /** @var HasMany $field */
        $field = $request->getField();

        /** @var ModelResource $resource */
        $resource = $field->getResource();

        $item = $resource
            ->setItemID($request->input('_key', false))
            ->getItemOrInstance();

        $update = $item->exists;
        $relation = $parent?->{$field->getRelationName()}();

        $field->fillCast($parent, $request->getResource()?->getModelCast());

        $action = $update
            ? static fn (Model $data) => $resource->getRoute('crud.update', $data->getKey())
            : static fn (?Model $data) => $resource->getRoute('crud.store');

        $isAsync = $field->isAsync();

        $getFields = static function () use ($resource, $field, $isAsync, $parent, $update) {
            $fields = $resource->getFormFields();

            $fields->onlyFields()->each(static fn (FieldContract $nestedFields): FieldContract => $nestedFields->setParent($field));

            return $fields->when(
                $field->getRelation() instanceof MorphOneOrMany,
                static fn (Fields $f) => $f->push(
                    Hidden::make($field->getRelation()?->getMorphType())
                        ->setValue($parent::class)
                )
            )->when(
                $update,
                static fn (Fields $f) => $f->push(
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

        $formName = "{$resource->getUriKey()}-unique-" . ($item?->getKey() ?? "create");

        return (string) FormBuilder::make($action($item))
            ->fields($getFields)
            ->reactiveUrl(
                static fn (): string => moonshineRouter()
                    ->getEndpoints()
                    ->reactive(page: $resource->getFormPage(), resource: $resource, extra: ['key' => $item?->getKey()])
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
                    $resource->getModelCast()
                ),
                static fn (FormBuilderContract $form): FormBuilderContract => $form->fillCast(
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
            ->onBeforeFieldsRender(static fn (Fields $fields): Renderables => $fields->exceptElements(
                static fn (mixed $field): bool => $field instanceof ModelRelationField
                    && $field->isToOne()
                    && $field->getColumn() === $relation->getForeignKeyName()
            ))
            ->buttons($resource->getCustomFormButtons())
            ->redirect($isAsync ? null : $field->getRedirectAfter($parent));
    }
}
