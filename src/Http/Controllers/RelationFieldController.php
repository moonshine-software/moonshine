<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller as BaseController;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Fields\Relationships\ModelRelationField;
use MoonShine\Http\Requests\MoonshineFormRequest;
use MoonShine\Http\Requests\Resources\ViewAnyFormRequest;
use MoonShine\MoonShineRequest;
use Throwable;

class RelationFieldController extends BaseController
{
    protected ?ModelRelationField $field = null;

    protected bool $fieldNotFound = false;

    protected ?ResourceContract $fieldResource = null;

    /**
     * @throws Throwable
     */
    public function index(ViewAnyFormRequest $request): View
    {
        $this->resolveFieldData($request);

        if ($this->fieldNotFound) {
            return $this->fieldNotFoundResponse();
        }

        return view($this->fieldResource->itemsView(), [
            'resource' => $this->fieldResource,
            'resources' => $this->fieldResource->isPaginationUsed()
                ? $this->fieldResource->paginate()
                : $this->fieldResource->items(),
        ]);
    }

    /**
     * @throws Throwable
     */
    public function resolveFieldData(MoonShineRequest $request): void
    {
        $resource = $request->getResource();

        $this->field = $resource
            ->getFields()
            ->findByRelation($request->get('_field_relation', ''));

        if (is_null($this->field) || ! $this->field->hasResource()) {
            $this->fieldNotFound = true;
        } else {
            $this->fieldResource = $this->field->getResource();

            $this->fieldResource->customBuilder(
                $resource->getItemOrInstance()->{$this->field->getRelationName()}()
            );
        }
    }

    private function fieldNotFoundResponse(): View
    {
        return view('moonshine::components.alert', [
            'type' => 'error',
            'slot' => 'Field not found',
        ]);
    }

    /**
     * @throws Throwable
     */
    public function form(ViewAnyFormRequest $request): View
    {
        $this->resolveFieldData($request);

        if ($this->fieldNotFound) {
            return $this->fieldNotFoundResponse();
        }

        $item = $request->getItemOrInstance();
        $model = $this->fieldResource->getModel();

        $foreignKeyName = $item->{$this->field->relation()}()
            ->getForeignKeyName();

        if (! $id = $request->get('_related_key')) {
            $id = MoonshineFormRequest::create(
                $request->headers->get('referer')
            )->getIdBySegment();
        }

        $model->{$foreignKeyName} = $id;

        return view($this->fieldResource->formView(), [
            'resource' => $this->fieldResource,
            'item' => $model,
        ]);
    }
}
