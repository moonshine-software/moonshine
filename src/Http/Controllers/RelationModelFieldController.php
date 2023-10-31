<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use Illuminate\Database\Eloquent\Relations\MorphOneOrMany;
use MoonShine\Contracts\Fields\Relationships\HasAsyncSearch;
use MoonShine\Exceptions\ResourceException;
use MoonShine\Fields\Relationships\MorphTo;
use MoonShine\Http\Requests\Relations\RelationModelFieldDeleteRequest;
use MoonShine\Http\Requests\Relations\RelationModelFieldRequest;
use MoonShine\Http\Requests\Relations\RelationModelFieldStoreRequest;
use MoonShine\Http\Requests\Relations\RelationModelFieldUpateRequest;
use MoonShine\Resources\ModelResource;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class RelationModelFieldController extends MoonShineController
{
    /**
     * @throws Throwable
     */
    public function search(RelationModelFieldRequest $request): Response
    {
        $term = $request->get('query');

        $field = $request->getPageField();

        if (! $field instanceof HasAsyncSearch || empty($term)) {
            return response()->json();
        }

        /* @var ModelResource $resource */
        $resource = $field->getResource();

        $model = $resource->getModel();

        $searchColumn = $field->asyncSearchColumn() ?? $resource->column();

        if ($field instanceof MorphTo) {
            $field->resolveFill([], $model);

            $morphClass = $request->get($field->getMorphType());
            $model = new $morphClass();
            $searchColumn = $field->getSearchColumn($morphClass);
        }

        $query = $model->newModelQuery();

        if (is_closure($field->asyncSearchQuery())) {
            $query = value(
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

        return response()->json(
            $query->get()->map(
                fn ($model): array => $field->asyncResponseData($model, $searchColumn)
            )->toArray()
        );
    }

    public function searchRelations(RelationModelFieldRequest $request): mixed
    {
        /* @var ModelResource $parentResource */
        $parentResource = $request->getResource();

        $parentItem = $parentResource->getItemOrInstance();

        $field = $request->getField();

        $field?->resolveFill(
            $parentItem->toArray(),
            $parentItem
        );

        return $field?->value();
    }

    /**
     * @throws Throwable
     * @throws ResourceException
     */
    public function store(RelationModelFieldStoreRequest $request): Response
    {
        return $this->updateOrCreate($request);
    }

    /**
     * @throws ResourceException
     * @throws Throwable
     */
    public function update(RelationModelFieldUpateRequest $request): Response
    {
        return $this->updateOrCreate($request);
    }

    public function delete(RelationModelFieldDeleteRequest $request): Response
    {
        /* @var ModelResource $parentResource */
        $parentResource = $request->getResource();

        $parentItem = $parentResource->getItemOrInstance();

        $field = $request->getField()?->resolveFill(
            $parentItem->toArray(),
            $parentItem
        );

        $fields = $field?->getFields();

        /* @var ModelResource $resource */
        $resource = $field->getResource();

        if ($fields->isEmpty()) {
            $fields = $resource->getFormFields();
        }

        $redirectRoute = $request->get('_redirect', $parentResource->redirectAfterDelete());

        if ($this->tryOrRedirect(static fn () => $resource->delete(
            $request->getFieldItemOrFail(),
            $fields->onlyFields()
        ), $redirectRoute) instanceof RedirectResponse) {
            return redirect($redirectRoute);
        }

        if ($request->ajax()) {
            return $this->json(message: __('moonshine::ui.deleted'));
        }

        $this->toast(
            __('moonshine::ui.deleted'),
            'delete'
        );

        return redirect($redirectRoute);
    }

    /**
     * @throws Throwable
     * @throws ResourceException
     */
    protected function updateOrCreate(
        RelationModelFieldRequest $request
    ): Response {
        /* @var ModelResource $parentResource */
        $parentResource = $request->getResource();
        $parentItem = $parentResource->getItemOrInstance();

        $field = $request->getField()?->resolveFill(
            $parentItem->toArray(),
            $parentItem
        );

        /* @var ModelResource $resource */
        $resource = $field->getResource();

        $relation = $field->getRelation();

        if ($request->isMethod('POST')) {
            $item = $resource->getModel();
            $item->{$relation->getForeignKeyName()} = $parentItem->getKey();

            if ($relation instanceof MorphOneOrMany) {
                $item->{$relation->getQualifiedMorphType()} = $parentItem::class;
            }
        } else {
            $item = $request->getFieldItemOrFail();
        }

        $validator = $resource->validate($item);

        $redirectRoute = $request->get('_redirect', $parentResource->redirectAfterSave());

        if ($request->isAttemptingPrecognition()) {
            return response()->json(
                $validator->errors(),
                $validator->fails()
                    ? Response::HTTP_UNPROCESSABLE_ENTITY
                    : Response::HTTP_OK
            );
        }

        if ($validator->fails()) {
            return back()
                ->withErrors($validator, $field->getRelationName())
                ->withInput();
        }

        $fields = $field->getFields();

        if ($fields->isEmpty()) {
            $fields = $resource->getFormFields();
        }

        if ($this->tryOrRedirect(
            static fn () => $resource->save(
                $item,
                $fields->onlyFields()
            ),
            $redirectRoute
        ) instanceof RedirectResponse) {
            return redirect($redirectRoute);
        }

        $this->toast(
            __('moonshine::ui.saved'),
            'success'
        );

        if ($request->ajax()) {
            return $this->json(message: __('moonshine::ui.saved'));
        }

        return redirect($redirectRoute);
    }
}
