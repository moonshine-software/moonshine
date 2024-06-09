<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Controllers;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOneOrMany;
use MoonShine\Laravel\Collections\Fields;
use MoonShine\Laravel\Contracts\Fields\HasAsyncSearch;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\Laravel\Fields\Relationships\ModelRelationField;
use MoonShine\Laravel\Fields\Relationships\MorphTo;
use MoonShine\Laravel\Http\Requests\Relations\RelationModelFieldRequest;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Laravel\Support\DBOperators;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\UI\Collections\MoonShineRenderElements;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Components\Table\TableRowRenderer;
use MoonShine\UI\Fields\Field;
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

        if (! $field instanceof HasAsyncSearch) {
            return response()->json();
        }

        /* @var \MoonShine\Laravel\Resources\ModelResource $resource */
        $resource = $field->getResource();

        $model = $resource->getModel();

        $searchColumn = $field->asyncSearchColumn() ?? $resource->column();

        if ($field instanceof MorphTo) {
            $field->fillCast([], $resource->getModelCast());

            $morphClass = $field->getWrapName()
                ? data_get($request->get($field->getWrapName(), []), $field->getMorphType())
                : $request->get($field->getMorphType());

            $model = new $morphClass();
            $searchColumn = $field->getSearchColumn($morphClass);
        }

        $query = $model->newModelQuery();

        if (! is_null($field->asyncSearchQuery())) {
            $query = value(
                $field->asyncSearchQuery(),
                $query,
                $request,
                $field
            );
        }

        $term = $request->get('query');
        $values = $request->get($field->getColumn(), '') ?? '';

        $except = is_array($values)
            ? array_keys($values)
            : array_filter(explode(',', (string) $values));

        $offset = $request->get('offset', 0);

        $query->when(
            $term,
            fn (Builder $q) => $q->where(
                $searchColumn,
                DBOperators::byModel($q->getModel())->like(),
                "%$term%"
            )
        )
            ->whereNotIn($model->getKeyName(), $except)
            ->offset($offset)
            ->limit($field->asyncSearchCount());

        return response()->json(
            $query->get()->map(
                fn ($model): array => $field->getAsyncSearchOption($model, $searchColumn)->toArray()
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

        $value = $field?->value();

        if ($value instanceof TableBuilder && $request->filled('_key')) {
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
            ->setItemID($request->get('_key', false))
            ->getItemOrInstance();

        $update = $item->exists;
        $relation = $parent?->{$field->getRelationName()}();

        $field->fillCast($parent, $request->getResource()?->getModelCast());

        $action = $update
            ? static fn (Model $data) => $resource->route('crud.update', $data->getKey())
            : static fn (?Model $data) => $resource->route('crud.store');

        $isAsync = $field->isAsync();

        $getFields = function () use ($resource, $field, $isAsync, $parent, $update) {
            $fields = $resource->getFormFields();

            $fields->onlyFields()->each(fn (Field $nestedFields): Field => $nestedFields->setParent($field));

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

        $formName = "{$resource->uriKey()}-unique-" . ($item?->getKey() ?? "create");

        return (string) FormBuilder::make($action($item))
            ->fields($getFields)
            ->reactiveUrl(
                fn (): string => moonshineRouter()
                    ->getEndpoints()
                    ->reactive(page: $resource->formPage(), resource: $resource, extra: ['key' => $item?->getKey()])
            )
            ->name($formName)
            ->switchFormMode(
                $isAsync,
                array_filter([
                    $resource->listEventName($field->getRelationName()),
                    $update ? null : AlpineJs::event(JsEvent::FORM_RESET, $formName),
                ])
            )
            ->when(
                $update,
                fn (FormBuilder $form): FormBuilder => $form->fillCast(
                    $item,
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
            ->onBeforeFieldsRender(fn (Fields $fields): MoonShineRenderElements => $fields->exceptElements(
                fn (mixed $field): bool => $field instanceof ModelRelationField
                    && $field->toOne()
                    && $field->getColumn() === $relation->getForeignKeyName()
            ))
            ->buttons($resource->getFormButtons())
            ->redirect($isAsync ? null : $field->getRedirectAfter($parent));
    }
}
