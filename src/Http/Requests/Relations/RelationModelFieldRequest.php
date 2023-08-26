<?php

declare(strict_types=1);

namespace MoonShine\Http\Requests\Relations;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\Relationships\ModelRelationField;
use MoonShine\Http\Requests\MoonshineFormRequest;
use MoonShine\MoonShine;
use MoonShine\Resources\ModelResource;
use Throwable;

class RelationModelFieldRequest extends MoonshineFormRequest
{
    protected ?ModelResource $resource = null;

    protected ?ModelResource $parentResource = null;

    protected ?ModelRelationField $field = null;

    protected ?Model $item = null;

    protected ?Model $parentItem = null;

    public function getResourceUri(): ?string
    {
        return $this->route('_resourceUri');
    }

    public function getRelationName(): string
    {
        return request('_relation');
    }

    /**
     * @throws Throwable
     */
    public function getField(): ?ModelRelationField
    {
        if (! is_null($this->field)) {
            return $this->field;
        }

        $form = $this->getPage()
            ->getComponents()
            ->findForm(request('_form'));

        $this->field = $form->getFields()
            ->onlyRelationFields()
            ->findByRelation($this->getRelationName());

        return $this->field;
    }

    public function getParentResource(): ?ModelResource
    {
        if (! is_null($this->parentResource)) {
            return $this->parentResource;
        }

        $this->parentResource = MoonShine::getResourceFromUriKey(
            $this->get('_parent')
        );

        return $this->parentResource;
    }

    public function getItem(): Model
    {
        if (! is_null($this->item)) {
            return $this->item;
        }

        $this->item = $this->getResource()->getItemOrInstance();

        return $this->item;
    }

    public function getParentItem(): Model
    {
        if (! is_null($this->parentItem)) {
            return $this->parentItem;
        }

        $this->parentItem = $this->getParentResource()->getItemOrInstance();

        return $this->parentItem;
    }
}
