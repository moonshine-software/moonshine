<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller as BaseController;
use MoonShine\Exceptions\ResourceException;
use MoonShine\Http\Requests\Relations\RelationModelFieldRequest;
use MoonShine\Http\Requests\Relations\RelationModelFieldStoreRequest;
use MoonShine\Http\Requests\Relations\RelationModelFieldUpateRequest;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class RelationModelFieldController extends BaseController
{
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
