<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use App\Models\Item;
use Illuminate\Database\Eloquent\Relations\MorphOneOrMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use MoonShine\Contracts\Fields\Relationships\HasAsyncSearch;
use MoonShine\Exceptions\ResourceException;
use MoonShine\Fields\Relationships\MorphTo;
use MoonShine\Http\Requests\Relations\RelationModelFieldDeleteRequest;
use MoonShine\Http\Requests\Relations\RelationModelFieldRequest;
use MoonShine\Http\Requests\Relations\RelationModelFieldStoreRequest;
use MoonShine\Http\Requests\Relations\RelationModelFieldUpateRequest;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class RelationModelFieldController extends MoonShineController
{
    /**
     * @throws Throwable
     */
    public function search(RelationModelFieldRequest $request): JsonResponse
    {
        $term = $request->get('query');
        $extra = $request->get('extra');

        $field = $request->getComponentField();

        if (! $field instanceof HasAsyncSearch || empty($term)) {
            return response()->json();
        }

        $resource = $field->getResource();

        $model = $resource->getModel();

        $searchColumn = $field->asyncSearchColumn() ?? $resource->column();

        if ($field instanceof MorphTo) {
            $morphClass = $extra;
            $model = new $morphClass();
            $searchColumn = $field->getSearchColumn($morphClass);
        }

        $query = $model->newModelQuery();

        if (is_closure($field->asyncSearchQuery())) {
            $query = call_user_func(
                $field->asyncSearchQuery(),
                $query,
                $request
            );
        }

        $query = $query->where(
            $searchColumn,
            'LIKE',
            "%$term%"
        )->limit($field->asyncSearchCount());

        return response()->json(
            $query->get()->map(
                fn ($model): array => $field->asyncResponseData($model, $searchColumn)
            )->toArray()
        );
    }

    public function searchRelations(RelationModelFieldRequest $request)
    {
        $parentResource = $request->getResource();

        /**
         * @var Item $parentItem
         */
        $parentItem = $parentResource->getItemOrInstance();

        $field = $request->getField();

        $field->resolveFill(
            $parentItem->toArray(),
            $parentItem
        );

        return $field->value();
    }

    public function store(RelationModelFieldStoreRequest $request): JsonResponse|RedirectResponse
    {
        return $this->updateOrCreate($request);
    }

    public function update(RelationModelFieldUpateRequest $request): JsonResponse|RedirectResponse
    {
        return $this->updateOrCreate($request);
    }

    public function delete(RelationModelFieldDeleteRequest $request): RedirectResponse
    {
        $parentResource = $request->getResource();

        $parentItem = $parentResource->getItemOrInstance();

        $field = $request->getField()->resolveFill(
            $parentItem->toArray(),
            $parentItem
        );

        $fields = $field->getFields();

        $resource = $field->getResource();

        if ($fields->isEmpty()) {
            $fields = $resource->getFormFields();
        }

        $resource->delete(
            $request->getFieldItemOrFail(),
            $fields->onlyFields()
        );

        $this->toast(
            __('moonshine::ui.deleted'),
            'delete'
        );

        return $request->redirectRoute(
            $parentResource->redirectAfterSave()
        );
    }

    /**
     * @throws Throwable
     * @throws ResourceException
     */
    protected function updateOrCreate(
        RelationModelFieldRequest $request
    ): JsonResponse|RedirectResponse {
        $parentResource = $request->getResource();
        $parentItem = $parentResource->getItemOrInstance();

        $field = $request->getField()->resolveFill(
            $parentItem->toArray(),
            $parentItem
        );

        $resource = $field->getResource();

        $relation = $field->getRelation();

        if ($request->isMethod('POST')) {
            $item = $resource->getModel();
            $item->{$relation->getForeignKeyName()} = $parentItem->getKey();

            if ($relation instanceof MorphOneOrMany) {
                $item->{$relation->getQualifiedMorphType()} = $parentItem::class;
            }
        } else {
            $item = $request->getFieldItemOrFail();
        }

        $validator = $resource->validate($item);

        $redirectRoute = redirect(
            to_page(
                $parentResource,
                'form-page',
                ['resourceItem' => $parentItem->getKey()]
            )
        );

        if ($request->isAttemptingPrecognition()) {
            return response()->json(
                $validator->errors(),
                $validator->fails()
                    ? ResponseAlias::HTTP_UNPROCESSABLE_ENTITY
                    : ResponseAlias::HTTP_OK
            );
        }

        if ($validator->fails()) {
            return $redirectRoute
                ->withErrors($validator, $field->getRelationName())
                ->withInput();
        }

        $fields = $field->getFields();

        if ($fields->isEmpty()) {
            $fields = $resource->getFormFields();
        }

        $resource->save(
            $item,
            $fields->onlyFields()
        );

        $this->toast(
            __('moonshine::ui.saved'),
            'success'
        );

        return $request->redirectRoute(
            $parentResource->redirectAfterSave()
        );
    }
}
