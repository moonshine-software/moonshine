<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOneOrMany;
use MoonShine\Collections\MoonShineRenderElements;
use MoonShine\Components\FormBuilder;
use MoonShine\Components\TableBuilder;
use MoonShine\Contracts\Fields\Relationships\HasAsyncSearch;
use MoonShine\Enums\JsEvent;
use MoonShine\Fields\Field;
use MoonShine\Fields\Fields;
use MoonShine\Fields\Hidden;
use MoonShine\Fields\Relationships\HasMany;
use MoonShine\Fields\Relationships\ModelRelationField;
use MoonShine\Fields\Relationships\MorphTo;
use MoonShine\Http\Requests\Relations\RelationModelFieldRequest;
use MoonShine\Resources\ModelResource;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\DBOperators;
use MoonShine\Support\TableRowRenderer;
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

        /* @var \MoonShine\Resources\ModelResource $resource */
        $resource = $field->getResource();

        $model = $resource->getModel();

        $searchColumn = $field->asyncSearchColumn() ?? $resource->column();

        if ($field instanceof MorphTo) {
            $field->resolveFill([], $model);

            $morphClass = $field->getWrapName()
                ? data_get($request->get($field->getWrapName(), []), $field->getMorphType())
                : $request->get($field->getMorphType());

            $model = new $morphClass();
            $searchColumn = $field->getSearchColumn($morphClass);
        }

        $query = $model->newModelQuery();

        if (is_closure($field->asyncSearchQuery())) {
            $query = value(
                $field->asyncSearchQuery(),
                $query,
                $request,
                $field
            );
        }

        $term = $request->get('query');
        $values = $request->get($field->column(), '') ?? '';

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
                fn ($model): array => $field->asyncResponseData($model, $searchColumn)
            )->toArray()
        );
    }

    /**
     * @throws Throwable
     */
    public function searchRelations(RelationModelFieldRequest $request): mixed
    {
        /* @var \MoonShine\Resources\ModelResource $parentResource */
        $parentResource = $request->getResource();

        $parentItem = $parentResource->getItemOrInstance();

        $field = $request->getField();

        $field?->resolveFill(
            $parentItem->toArray(),
            $parentItem
        );

        $value = $field?->value();

        if ($value instanceof TableBuilder && $request->filled('_key')) {
            return TableRowRenderer::make(
                $value,
                $request->get('_key'),
                $request->integer('_index'),
            )->render();
        }

        return $value;
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
        $item = $field->getResource()
            ->setItemID($request->get('_key', ''))
            ->getItemOrInstance();
        $update = $item->exists;
        $relation = $parent?->{$field->getRelationName()}();

        $field->resolveFill($parent->toArray(), $parent);

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

        $formName = $resource->uriKey() . "-" . ($item?->getKey() ?? 'create');

        return (string) FormBuilder::make($action($item))
            ->fields($getFields)
            ->reactiveUrl(
                fn (): string => moonshineRouter()
                    ->reactive(key: $item?->getKey(), page: $resource->formPage(), resource: $resource)
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
                    && $field->column() === $relation->getForeignKeyName()
            ))
            ->buttons($resource->getFormButtons())
            ->redirect(
                $isAsync
                    ?
                    null
                    : moonshineRequest()
                    ->getResource()
                    ?->formPageUrl($parent)
            );
    }
}
