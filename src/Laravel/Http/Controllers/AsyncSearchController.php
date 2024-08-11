<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Controllers;

use MoonShine\Laravel\Contracts\Fields\HasAsyncSearchContract;
use MoonShine\Laravel\Fields\Relationships\MorphTo;
use MoonShine\Laravel\Http\Requests\Relations\RelationModelFieldRequest;
use MoonShine\Laravel\Support\DBOperators;
use Throwable;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Contracts\Database\Eloquent\Builder;

final class AsyncSearchController extends MoonShineController
{
    /**
     * @throws Throwable
     */
    public function __invoke(RelationModelFieldRequest $request): Response
    {
        $field = $request->getPageField();

        if (! $field instanceof HasAsyncSearchContract) {
            return response()->json();
        }

        /* @var \MoonShine\Laravel\Resources\ModelResource $resource */
        $resource = $field->getResource();

        $model = $resource->getModel();

        $searchColumn = $field->getAsyncSearchColumn() ?? $resource->getColumn();

        if ($field instanceof MorphTo) {
            $field->fillCast([], $resource->getModelCast());

            $morphClass = $field->getWrapName()
                ? data_get($request->input($field->getWrapName(), []), $field->getMorphType())
                : $request->input($field->getMorphType());

            $model = new $morphClass();
            $searchColumn = $field->getSearchColumn($morphClass);
        }

        $query = $resource->resolveQuery();
        $term = $request->input('query');

        if (! is_null($field->getAsyncSearchQuery())) {
            $query = value(
                $field->getAsyncSearchQuery(),
                $query,
                $term,
                $request,
                $field
            );
        }

        $values = $request->input($field->getColumn(), '') ?? '';

        $except = is_array($values)
            ? array_keys($values)
            : array_filter(explode(',', (string) $values));

        $offset = $request->input('offset', 0);

        $query->when(
            $term && is_null($field->getAsyncSearchQuery()),
            static fn (Builder $q) => $q->where(
                $searchColumn,
                DBOperators::byModel($q->getModel())->like(),
                "%$term%"
            )
        )
            ->whereNotIn($model->getKeyName(), $except)
            ->offset($offset)
            ->limit($field->getAsyncSearchCount());

        return response()->json(
            $query->get()->map(
                static fn ($model): array => $field->getAsyncSearchOption($model, $searchColumn)->toArray()
            )->toArray()
        );
    }
}
