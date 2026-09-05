<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Controllers;

use Closure;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\Contracts\UI\TableBuilderContract;
use MoonShine\Crud\JsonResponse;
use MoonShine\Laravel\Exceptions\ModelRelationFieldException;
use MoonShine\Laravel\Fields\Relationships\BelongsToMany;
use MoonShine\Laravel\Http\Requests\Relations\BelongsToManyPivotRequest;
use MoonShine\Laravel\Http\Requests\Relations\RelationModelFieldRequest;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\FormMethod;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\Support\Enums\ToastType;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Fields\Hidden;
use MoonShine\UI\Fields\Preview;
use MoonShine\UI\Fields\Select;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class BelongsToManyPivotController extends MoonShineController
{
    /**
     * @throws Throwable
     */
    public function formComponent(RelationModelFieldRequest $request): string
    {
        $parent = $request->getResourceOrFail()->getItemOrInstance();

        /** @var null|BelongsToMany $field */
        $field = $request->getPageField(BelongsToMany::class);

        if ($field === null) {
            throw ModelRelationFieldException::notFound();
        }

        /** @var ModelResource $resource */
        $resource = $field->getResourceOrFail();

        $relatedKey = $request->string('_key')->value();
        $update = filled($relatedKey);

        $field->fillCast($parent, $request->getResourceOrFail()->getCaster());

        /** @var \Illuminate\Database\Eloquent\Relations\BelongsToMany<Model, Model> $relation */
        $relation = $field->getRelationFor($parent);

        if ($update) {
            $item = $relation
                ->wherePivot($relation->getRelatedPivotKeyName(), $relatedKey)
                ->firstOrFail();

            /** @var Model|null $pivot */
            $pivot = $item->getRelation($field->getPivotAs());
            $pivotData = $pivot?->toArray() ?? [];
        } else {
            $pivotData = [];
            $item = null;
        }

        /** @var ModelResource $parentResource */
        $parentResource = $request->getResourceOrFail();
        $parentPage = $request->getPage();

        $action = $update
            ? static fn (Model $data) => $parentResource->getRoute(
                'belongs-to-many-pivot.update',
                $parentResource->getCaster()->cast($data),
                query: [
                'pageUri' => $parentPage->getUriKey(),
                '_relation' => $field->getRelationName(),
                '_key' => $relatedKey,
            ],
            )
            : static fn (Model $data) => $parentResource->getRoute(
                'belongs-to-many-pivot.store',
                $parentResource->getCaster()->cast($data),
                query: [
                'pageUri' => $parentPage->getUriKey(),
                '_relation' => $field->getRelationName(),
                '_key' => $relatedKey,
            ],
            );

        $getFields = function () use ($field, $item, $update): array {
            $fields = $field->getFields();

            $fields->onlyFields()->each(
                static fn (FieldContract $nestedFields): FieldContract
                    => $nestedFields
                    ->setParent($field),
            );

            return $fields
                ->when(
                    $update,
                    fn (\MoonShine\Contracts\Core\DependencyInjection\FieldsContract $ctx)
                        => $ctx->prepend(Hidden::make('_method')->setValue('PUT'))->prepend(
                            Preview::make(
                                $field->getResourceColumnLabel(),
                            )->setValue(
                                $this->getRelatedLabel($field, $item),
                            ),
                        ),
                    fn (\MoonShine\Contracts\Core\DependencyInjection\FieldsContract $ctx)
                        => $ctx->prepend(
                            $this->buildRelatedSelect($field),
                        ),
                )
                ->push(
                    Hidden::make('_relation_name')
                        ->setValue($field->getRelationName()),
                )
                ->all();
        };

        $formName = "pivot-form-{$resource->getUriKey()}-" . ($update ? $relatedKey : 'create');
        $tableName = $field->getTableComponentName();

        return (string)FormBuilder::make($action($parent))
            ->fields($getFields)
            ->name($formName)
            ->when(
                $update,
                static fn (FormBuilderContract $form): FormBuilderContract
                    => $form
                    ->method(FormMethod::POST)
                    ->fillCast($pivotData, $resource->getCaster()),
            )
            ->async(events: array_filter([
                AlpineJs::event($field->getComponentEvent(), $tableName),
                $update ? null : AlpineJs::event(JsEvent::FORM_RESET, $formName),
            ]))
            ->submit(__('moonshine::ui.save'), ['class' => 'btn-primary']);
    }

    /**
     * @throws Throwable
     */
    private function buildRelatedSelect(BelongsToMany $field): Select
    {
        /** @var ModelResource $resource */
        $resource = $field->getResourceOrFail();
        $resourceColumn = $field->getResourceColumn();
        $relatedKeyName = $field->getRelatedKeyName();

        $select = Select::make(
            $field->getResourceColumnLabel(),
            '_key',
        )->required();

        if ($field->isAsyncSearch()) {
            return $select->async(
                url: $field->getAsyncSearchUrl(),
            )->searchable();
        }

        /** @var array<array-key, string> $options */
        $options = $resource
            ->getQuery()
            ->pluck($resourceColumn, $relatedKeyName)
            ->all();

        return $select->options($options)->searchable();
    }

    private function getRelatedLabel(BelongsToMany $field, ?Model $relatedItem): string
    {
        if (! $relatedItem instanceof Model) {
            return '';
        }

        $callback = $field->getFormattedValueCallback();

        if ($callback instanceof Closure) {
            /** @var string|int|float|bool|\Stringable|null $label */
            $label = $callback($relatedItem, 0, $field);

            return (string) $label;
        }

        /** @var string|int|float|bool|\Stringable|null $label */
        $label = data_get($relatedItem, $field->getResourceColumn(), '');

        return (string) $label;
    }

    /**
     * @throws Throwable
     */
    public function store(BelongsToManyPivotRequest $request): JsonResponse
    {
        $parent = $request->getResourceOrFail()->getItemOrInstance();

        /** @var null|BelongsToMany $field */
        $field = $request->getPageField(BelongsToMany::class);

        if ($field === null) {
            throw ModelRelationFieldException::notFound();
        }

        $relatedKey = $request->string('_key')->value();

        if (blank($relatedKey)) {
            return $this->json(
                message: __('moonshine::ui.saved_error'),
                messageType: ToastType::ERROR,
                status: Response::HTTP_BAD_REQUEST,
            );
        }

        $pivotData = $this->extractPivotData($request, $field);

        $field->getRelationFor($parent)->attach($relatedKey, $pivotData);

        return $this->json(
            message: __('moonshine::ui.saved'),
        );
    }

    /**
     * @throws Throwable
     */
    public function update(BelongsToManyPivotRequest $request): JsonResponse
    {
        $parent = $request->getResourceOrFail()->getItemOrInstance();

        /** @var null|BelongsToMany $field */
        $field = $request->getPageField(BelongsToMany::class);

        if ($field === null) {
            throw ModelRelationFieldException::notFound();
        }

        $relatedKey = $request->string('_key')->value();

        if (blank($relatedKey)) {
            return $this->json(
                message: __('moonshine::ui.saved_error'),
                messageType: ToastType::ERROR,
                status: Response::HTTP_BAD_REQUEST,
            );
        }

        $pivotData = $this->extractPivotData($request, $field);

        $field->getRelationFor($parent)->updateExistingPivot($relatedKey, $pivotData);

        return $this->json(
            message: __('moonshine::ui.saved'),
        );
    }

    /**
     * @throws Throwable
     */
    public function destroy(BelongsToManyPivotRequest $request): JsonResponse
    {
        $parent = $request->getResourceOrFail()->getItemOrInstance();

        /** @var null|BelongsToMany $field */
        $field = $request->getPageField(BelongsToMany::class);

        if ($field === null) {
            throw ModelRelationFieldException::notFound();
        }

        $relatedKey = $request->string('_key')->value();

        if (blank($relatedKey)) {
            return $this->json(
                message: __('moonshine::ui.saved_error'),
                messageType: ToastType::ERROR,
                status: Response::HTTP_BAD_REQUEST,
            );
        }

        $field->getRelationFor($parent)->detach($relatedKey);

        return $this->json(
            message: __('moonshine::ui.deleted'),
        );
    }

    /**
     * @throws Throwable
     */
    public function listComponent(RelationModelFieldRequest $request): string
    {
        /** @var ModelResource $parentResource */
        $parentResource = $request->getResourceOrFail();

        /** @var array<string, mixed> $params */
        $params = $request->only($parentResource->getQueryParamsKeys());
        $parentResource->setQueryParams($params);

        $parentItem = $parentResource->getItemOrInstance();

        /** @var null|BelongsToMany $field */
        $field = $request->getPageField(BelongsToMany::class);

        if ($field === null) {
            throw ModelRelationFieldException::notFound();
        }

        $field->fillCast(
            $parentItem,
            $parentResource->getCaster(),
        );

        $value = $field->getComponent();

        if ($value instanceof TableBuilderContract && $request->filled('_key')) {
            return (string)$this->responseWithTable($value, $field->getResource());
        }

        return (string)$value;
    }

    /**
     * @return array<string, mixed>
     */
    private function extractPivotData(RelationModelFieldRequest $request, BelongsToMany $field): array
    {
        $pivotData = [];

        foreach ($field->getFields()->onlyFields() as $pivotField) {
            $column = $pivotField->getColumn();
            $value = $request->input($column);

            if ($pivotField->isCanApply()) {
                $apply = $pivotField->apply(
                    static fn ($data): mixed => data_set($data, $column, $value),
                    $value,
                );

                $pivotData[$column] = data_get($apply, $column);
            } else {
                $pivotData[$column] = $value;
            }
        }

        return $pivotData;
    }
}
