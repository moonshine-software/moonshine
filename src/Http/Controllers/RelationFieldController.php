<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller as BaseController;
use MoonShine\Fields\Field;
use MoonShine\Http\Requests\Resources\ViewAnyFormRequest;
use MoonShine\MoonShineRequest;
use MoonShine\Resources\Resource;
use Throwable;

class RelationFieldController extends BaseController
{
    protected ?Field $field;

    protected bool $fieldNotFound = false;

    protected ?Resource $fieldResource;

    /**
     * @throws Throwable
     */
    public function __construct(MoonShineRequest $request)
    {
        $this->field = $request->getResource()
            ->getFields()
            ->findByRelation($request->get('_field_relation', ''));

        if (is_null($this->field) || ! $this->field->hasRelationship() || ! $this->field->resource()) {
            $this->fieldNotFound = true;
        } else {
            $this->fieldResource = $this->field->resource()->relatable();

            $this->fieldResource->customBuilder(
                $request->getItemOrInstance()->{$this->field->relation()}()
            );
        }
    }

    public function index(ViewAnyFormRequest $request): View
    {
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

    public function form(ViewAnyFormRequest $request): View
    {
        if ($this->fieldNotFound) {
            return $this->fieldNotFoundResponse();
        }

        $item = $request->getItemOrInstance();
        $model = $this->fieldResource->getModel();

        $foreignKeyName = $item->{$this->field->relation()}()
            ->getForeignKeyName();

        if (! $id = $request->get('_related_key')) {
            $id = MoonShineRequest::create(
                $request->headers->get('referer')
            )->getIdBySegment();
        }

        $model->{$foreignKeyName} = $id;

        return view($this->fieldResource->formView(), [
            'resource' => $this->fieldResource,
            'item' => $model,
        ]);
    }

    private function fieldNotFoundResponse(): View
    {
        return view('moonshine::components.alert', [
            'type' => 'error',
            'slot' => 'Field not found',
        ]);
    }
}
