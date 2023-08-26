<?php

declare(strict_types=1);

namespace MoonShine\Http\Requests\Relations;

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
            ->getFields()
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

        $this->field = $this->getResource()
            ->getFields()
            ->onlyRelationFields()
            ->findByRelation($this->getRelationName());

        return $this->field;
    }
}
