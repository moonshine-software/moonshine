<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller as BaseController;
use MoonShine\Contracts\Fields\Relationships\HasAsyncSearch;
use MoonShine\Exceptions\FieldException;
use MoonShine\Exceptions\ResourceException;
use MoonShine\Fields\Relationships\MorphTo;
use MoonShine\Http\Requests\Relations\RelationModelFieldRequest;
use MoonShine\Http\Requests\Relations\RelationModelFieldStoreRequest;
use MoonShine\Http\Requests\Relations\RelationModelFieldUpateRequest;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class RelationModelFieldController extends BaseController
{
    /**
     * @throws Throwable
     */
    public function search(RelationModelFieldRequest $request): JsonResponse
    {
        $term = $request->get('query');
        $extra = $request->get('extra');

        $field = $request->getField();

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

        if (is_closure($field->asyncSearchValueCallback())) {
            $values = $query->get()->mapWithKeys(
                fn ($relatedItem): array => [
                    $relatedItem->getKey() => ($field->asyncSearchValueCallback())($relatedItem),
                ]
            );
        } else {
            $values = $query->pluck(
                $searchColumn,
                $model->getKeyName()
            );
        }

        return response()->json($values->toArray());
    }

    public function store(RelationModelFieldStoreRequest $request): JsonResponse|RedirectResponse
    {
        return $this->updateOrCreate($request);
    }

    public function update(RelationModelFieldUpateRequest $request): JsonResponse|RedirectResponse
    {
        return $this->updateOrCreate($request);
    }

    /**
     * @throws Throwable
     * @throws ResourceException
     */
    protected function updateOrCreate(
        RelationModelFieldRequest $request
    ): JsonResponse|RedirectResponse {
        $parentResource = $request->getParentResource();
        $resource = $request->getResource();

        if (is_null($parentResource) || is_null($resource)) {
            throw new FieldException('Resources is required');
        }

        $relationName = $request->getRelationName();

        $parentItem = $request->getParentItem();
        $relation = $parentItem->{$relationName}();

        if ($request->isMethod('POST')) {
            $item = $resource->getItemOrInstance();

            $item->{$relation->getForeignKeyName()} = $parentResource->getItemID();
        } else {
            $item = $resource->getModel()
                ->newModelQuery()
                ->where($resource->getModel()->getKeyName(), request($resource->getModel()->getKeyName()))
                ->first();
        }

        $validator = $resource->validate($item);

        $redirectRoute = redirect(
            to_page(
                $parentResource,
                'form-page',
                ['resourceItem' => $parentResource->getItem()?->getKey()]
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
                ->withErrors($validator, $relationName)
                ->withInput();
        }


        /* @var HasFields $field */
        $field = $parentResource
            ->getOutsideFields()
            ->findByRelation($request->getRelationName());

        $fields = $field->getFields();

        if ($fields->isEmpty()) {
            $fields = $resource->getFormFields();
        }

        $resource->save(
            $item,
            $fields->onlyFields()
        );

        return $request->redirectRoute(
            $parentResource->redirectAfterDelete()
        );
    }
}
