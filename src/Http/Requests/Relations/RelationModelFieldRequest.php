<?php

declare(strict_types=1);

namespace MoonShine\Http\Requests\Relations;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\Relationships\ModelRelationField;
use MoonShine\Http\Requests\MoonshineFormRequest;
use MoonShine\Resources\ModelResource;

class RelationModelFieldRequest extends MoonshineFormRequest
{
    protected ?ModelResource $relationResource = null;

    protected ?ModelResource $parentResource = null;

    protected ?ModelRelationField $relationField = null;

    protected ?Model $parentItem = null;

    public function relationResource(): ModelResource
    {
        if(! is_null($this->relationResource)) {
            return $this->relationResource;
        }

        $this->relationResource = $this->relationField()
            ->getResource();

        return $this->relationResource;
    }

    public function getRelationName(): string
    {
        return request('_relation');
    }

    public function relationField(): ?ModelRelationField
    {
        if(! is_null($this->relationField)) {
            return $this->relationField;
        }

        $fields = $this->parentResource()
            ->getFormFields()
            ->onlyRelationFields();

        $this->relationField = $fields->findByRelation(
            $this->getRelationName()
        );

        return $this->relationField;
    }

    public function parentResource(): ModelResource
    {
        if(! is_null($this->parentResource)) {
            return $this->parentResource;
        }

        $this->parentResource = $this->getResource();

        return $this->parentResource;
    }

    public function parentItem(): Model
    {
        if(! is_null($this->parentItem)) {
            return $this->parentItem;
        }

        $this->parentItem = $this->parentResource()->getItem();

        return $this->parentItem;
    }
}
