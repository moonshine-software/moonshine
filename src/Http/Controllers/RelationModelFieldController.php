<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller as BaseController;
use MoonShine\Contracts\Fields\Relationships\HasAsyncSearch;
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
        $requestQuery = $request->get('query');

        $resource = $request->relationResource();
        $field = $request->relationField();

        if (! $field instanceof HasAsyncSearch || empty($requestQuery)) {
            return response()->json();
        }

        $field->resolveFill(
            $request->parentItem()->toArray(),
            $request->parentItem()
        );

        $related = $field->getRelatedModel();
        $searchColumn = $field->asyncSearchColumn() ?? $resource->column();

        if ($field instanceof MorphTo) {
            $morphClass = request('extra');
            $related = new $morphClass();
            $searchColumn = $field->getSearchColumn($morphClass);
        }

        $query = $field->resolveValuesQuery();

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
            "%$requestQuery%"
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
                $related->getKeyName()
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
        $parentResource = $request->parentResource();
        $resource = $request->relationResource();
        $relationName = $request->getRelationName();

        $parentItem = $request->parentItem();
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
                ['resourceItem' => $parentResource->getItem()->getKey()]
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

        $fields = $request->relationField()->getFields();

        if($fields->isEmpty()) {
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
