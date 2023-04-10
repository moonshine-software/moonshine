<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use MoonShine\Fields\BelongsToMany;
use MoonShine\MoonShine;
use Throwable;

class SearchController extends BaseController
{
    /**
     * @throws Throwable
     */
    public function relations(string $type)
    {
        abort_if(! request()->has(['resource', 'column', 'query']), 404);

        $response = [];
        $resource = MoonShine::getResourceFromUriKey(request('resource'));
        $item = request('id')
            ? $resource->getModel()->newModelQuery()->findOrFail(request('id'))
            : $resource->getModel();

        $field = $resource->getFields()->findFieldByColumn(request('column'));

        if ($field instanceof BelongsToMany && $type === class_basename($field)) {
            $request = request('query');
            $related = $field->getRelated($item);
            $query = $related->newModelQuery();

            if (is_callable($field->searchQuery())) {
                $query = call_user_func($field->searchQuery(), $query, $request);
            }

            if (is_callable($field->searchValueCallback())) {
                $values = $query
                    ->where($field->searchColumn() ?? $field->resourceTitleField(), 'LIKE', "%$request%")
                    ->get()
                    ->mapWithKeys(function ($relatedItem) use ($field) {
                        return [$relatedItem->getKey() => ($field->searchValueCallback())($relatedItem)];
                    });
            } else {
                $values = $query
                    ->where($field->searchColumn() ?? $field->resourceTitleField(), 'LIKE', "%$request%")
                    ->pluck($field->resourceTitleField(), $related->getKeyName());
            }

            $response = $values->toArray();
        }

        return response()->json($response);
    }
}
