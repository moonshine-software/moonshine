<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use MoonShine\Contracts\Fields\HasAsyncSearch;
use MoonShine\MoonShine;
use Throwable;

class SearchController extends BaseController
{
    /**
     * @throws Throwable
     */
    public function relations(): JsonResponse
    {
        abort_if(! request()->has(['resource', 'column']), 404);

        $response = [];
        $resource = MoonShine::getResourceFromUriKey(request('resource'));
        $item = request('id')
            ? $resource->getModel()->newModelQuery()->findOrFail(request('id'))
            : $resource->getModel();

        $field = $resource->getFields()->findFieldByColumn(request('column'));

        $requestQuery = request('query');

        if (($field instanceof HasAsyncSearch) && $requestQuery) {
            $related = $field->getRelated($item);
            $query = $related->newModelQuery();

            if (is_callable($field->asyncSearchQuery())) {
                $query = call_user_func($field->asyncSearchQuery(), $query, $requestQuery);
            }

            $query = $query->where($field->asyncSearchColumn() ?? $field->resourceTitleField(), 'LIKE', "%$requestQuery%")
                ->limit($field->asyncSearchCount());

            if (is_callable($field->asyncSearchValueCallback())) {
                $values = $query->get()
                    ->mapWithKeys(function ($relatedItem) use ($field) {
                        return [$relatedItem->getKey() => ($field->asyncSearchValueCallback())($relatedItem)];
                    });
            } else {
                $values = $query->pluck($field->resourceTitleField(), $related->getKeyName()); // TODO Why not $field->asyncSearchColumn() ?? $field->resourceTitleField()
            }

            $response = $values->toArray();
        }

        return response()->json($response);
    }
}
