<?php

declare(strict_types=1);

namespace MoonShine\Http\Requests\Relations;

use MoonShine\Fields\Field;
use MoonShine\Http\Requests\MoonshineFormRequest;
use MoonShine\Resources\ModelResource;
use Illuminate\Database\Eloquent\Model;

class RelationRequest extends MoonshineFormRequest
{
    protected ?ModelResource $relationResource = null;

    protected ?Field $relationField = null;

    protected ?ModelResource $parentResource = null;

    protected ?Model $parentItem = null;

    public function relationResource(): ModelResource
    {
        if(is_null($this->relationResource)) {
            $this->init();
        }

        return $this->relationResource;
    }

    public function relationField(): ?Field
    {
        if(is_null($this->relationField)) {
            $this->init();
        }

        return $this->relationField;
    }

    public function parentResource(): ModelResource
    {
        if(is_null($this->parentResource)) {
            $this->init();
        }

        return $this->parentResource;
    }

    public function parentItem(): Model
    {
        if(is_null($this->parentItem)) {
            $this->init();
        }

        return $this->parentItem;
    }

    protected function init(): void
    {
        $this->parentResource = $this->getResource();

        $this->parentItem = $this->parentResource->getItem();

        $fields = $this->parentResource->getOutsideFields()->onlyFields();

        $this->relationField = $fields->findByRelation(request('_relation'));

        $this->relationResource = $this->relationField->getResource();
    }
}