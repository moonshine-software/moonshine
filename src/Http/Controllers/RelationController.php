<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Routing\Controller as BaseController;
use MoonShine\Http\Requests\MoonshineFormRequest;

class RelationController extends BaseController
{
    public function store(MoonshineFormRequest $request)
    {
        return $this->updateOrCreate($request);
    }

    public function update(MoonshineFormRequest $request)
    {
        return $this->updateOrCreate($request);
    }

    public function destroy($resourceItem = null)
    {

    }

    protected function updateOrCreate(
        MoonshineFormRequest $request
    ) {
        $parentResource = $request->getResource();

        $parentItem = $parentResource->getItem();

        $fields = $parentResource->getOutsideFields()->onlyFields();

        $field = $fields->findByRelation(request('_relation'));

        $resource = $field->getResource();

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
                ->withErrors($validator)
                ->withInput();
        }

        $resource->save($item, $field->getFields());

        return $redirectRoute;
    }
}