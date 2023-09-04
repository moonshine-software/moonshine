<?php

declare(strict_types=1);

namespace MoonShine\Http\Requests\Relations;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\Fields;
use MoonShine\Fields\Relationships\ModelRelationField;
use MoonShine\Http\Requests\MoonshineFormRequest;
use MoonShine\Resources\ModelResource;
use Throwable;

class RelationModelFieldRequest extends MoonshineFormRequest
{
    protected ?ModelResource $resource = null;

    protected ?ModelRelationField $field = null;

    public function getRelationName(): string
    {
        return request('_relation');
    }

    /**
     * @throws Throwable
     */
    public function getComponentField(): ?ModelRelationField
    {
        if (! is_null($this->field)) {
            return $this->field;
        }

        $this->field = $this->getPageComponent(request('_component_name'))
            ->getFields($this->getPage()->uriKey())
            ->onlyRelationFields()
            ->findByRelation($this->getRelationName());

        return $this->field;
    }

    /**
     * @throws Throwable
     */
    public function getField(): ?ModelRelationField
    {
        if (! is_null($this->field)) {
            return $this->field;
        }

        $resource = $this->getResource();

        $fields = match($this->getPage()->uriKey()) {
            'index-page' => $resource->getIndexFields(),
            'show-page' => $resource->getDetailFields(),
            'form-page' => Fields::make(
                    empty($resource->formFields())
                        ? $resource->fields()
                        : $resource->formFields()
            )->filter()->formFields(),
            default => Fields::make($this->fields())
        };

        $this->field = $fields
            ->filter()
            ->onlyRelationFields()
            ->findByRelation($this->getRelationName());

        return $this->field;
    }

    public function getFieldItemOrFail(): Model
    {
        $resource = $this->getField()->getResource();

        return $resource
            ->getModel()
            ->newModelQuery()
            ->findOrFail(request($resource->getModel()->getKeyName()));
    }
}
