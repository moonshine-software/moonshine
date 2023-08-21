<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use MoonShine\Http\Requests\Relations\RelationRequest;
use MoonShine\Http\Requests\Relations\RelationStoreRequest;
use MoonShine\Http\Requests\Relations\RelationUpateRequest;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class RelationController extends BaseController
{
    public function store(RelationStoreRequest $request)
    {
        return $this->updateOrCreate($request);
    }

    public function update(RelationUpateRequest $request)
    {
        return $this->updateOrCreate($request);
    }

    public function destroy($resourceItem = null)
    {

    }

    protected function updateOrCreate(
        RelationRequest $request
    ) {
        $resource = $request->relationResource();

        $parentResource = $request->parentResource();

        $parentItem = $request->parentItem();

        if($request->isMethod('POST')) {
            $relation = $parentItem->{request('_relation')}();

            $item = $resource->getItemOrInstance();

            $item->{$relation->getForeignKeyName()} = $parentResource->getItemID();
        } else {
            $item = $resource->getModel()::where($resource->getModel()->getKeyName(), request('id'))->first();
        }

        $validator = $resource->validate($item);

        $redirectRoute = redirect(to_page($parentResource, 'form-page', ['resourceItem' => $parentResource->getItem()->getKey()]));

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
                ->withErrors($validator, request('_relation'))
                ->withInput();
        }

        $resource->save($item, $request->relationField()->getFields());

        return $request->redirectRoute(
            $parentResource->redirectAfterDelete()
        );
    }
}