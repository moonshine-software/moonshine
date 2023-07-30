<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use MoonShine\Contracts\Fields\Relationships\HasAsyncSearch;
use MoonShine\Fields\Relationships\MorphTo;
use MoonShine\MoonShine;
use Throwable;

class SearchController extends BaseController
{
    /**
     * @throws Throwable
     */
    public function relations(Request $request): JsonResponse
    {
        abort_if(! $request->has(['resource', 'column']), 404);

        $response = [];
        $resource = MoonShine::getResourceFromUriKey(request('resource'));

        $fields = $resource->getFields();
        $field = $fields->findByColumn(request('column'));

        $requestQuery = $request->get('query');

        if ($field instanceof HasAsyncSearch && $requestQuery) {
            if ($field instanceof MorphTo) {
                $morphClass = request('extra');
                $related = new $morphClass();
                $searchColumn = $field->getSearchColumn($morphClass);
            } else {
                $related = $field->getRelated($resource->getModel());
                $searchColumn = $field->asyncSearchColumn() ?? $field->resourceTitleField();
            }

            $query = $related->newModelQuery();

            if (is_callable($field->asyncSearchQuery())) {
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

            if (is_callable($field->asyncSearchValueCallback())) {
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

            $response = $values->toArray();
        }

        return response()->json($response);
    }
}
