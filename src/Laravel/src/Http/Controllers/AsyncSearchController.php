<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Controllers;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Crud\Contracts\Fields\HasAsyncSearchContract;
use MoonShine\Crud\Contracts\Resource\WithQueryBuilderContract;
use MoonShine\Laravel\Fields\Relationships\BelongsToMany;
use MoonShine\Laravel\Fields\Relationships\MorphTo;
use MoonShine\Laravel\Http\Requests\Relations\RelationModelFieldRequest;
use MoonShine\Laravel\Support\DBOperators;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

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

        if (! $resource instanceof WithQueryBuilderContract) {
            return response()->json();
        }

        $model = $resource->getDataInstance();

        $searchColumn = $field->getAsyncSearchColumn() ?? $resource->getColumn();
        $query = $resource->getQuery();
        if ($field instanceof MorphTo) {
            $field->fillCast([], $resource->getCaster());

            $morphClass = $field->getWrapName()
                ? data_get($request->input($field->getWrapName(), []), $field->getMorphType())
                : $request->input($field->getMorphType());

            if (! class_exists($morphClass)) {
                return response()->json();
            }

            /** @var Model $model */
            $model = new $morphClass();

            if (! $model instanceof Model) {
                return response()->json();
            }

            $searchColumn = $field->getSearchColumn($morphClass);
            $query = $model->newModelQuery();
        }

        $term = $request->input('query');

        if (! \is_null($field->getAsyncSearchQuery())) {
            $query = value(
                $field->getAsyncSearchQuery(),
                $query,
                $term,
                $request,
                $field
            );
        }

        $values = $request->input($field->getColumn(), '') ?? '';

        $except = \is_array($values)
            ? array_keys($values)
            : array_filter(explode(',', (string) $values));

        if ($field instanceof BelongsToMany && ! $field->isDeduplicate()) {
            $except = [];
        }

        $offset = $request->input('offset', 0);

        $query->when(
            $term && \is_null($field->getAsyncSearchQuery()),
            static fn (Builder $q) => $q->where(
                $searchColumn,
                DBOperators::byModel($q->getModel())->like(),
                "%$term%"
            )
        )
            ->whereNotIn($model->getQualifiedKeyName(), $except)
            ->offset($offset)
            ->limit($field->getAsyncSearchCount());

        return response()->json(
            $query->get()->map(
                static fn ($model): array => $field->getAsyncSearchOption($model, $searchColumn)->toArray()
            )->toArray()
        );
    }
}
