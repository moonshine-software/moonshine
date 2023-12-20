<?php

declare(strict_types=1);

namespace MoonShine\Http\Requests\Relations;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Enums\PageType;
use MoonShine\Exceptions\FieldException;
use MoonShine\Fields\Fields;
use MoonShine\Fields\Relationships\ModelRelationField;
use MoonShine\Http\Requests\MoonShineFormRequest;
use MoonShine\Resources\ModelResource;
use Throwable;

class RelationModelFieldRequest extends MoonShineFormRequest
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
    public function getPageField(): ?ModelRelationField
    {
        if (! is_null($this->field)) {
            return $this->field;
        }

        $fields = $this->getPage()->getComponents();

        if(request('_parent_field')) {
            $parent = $fields
                ->onlyFields()
                ->onlyHasFields()
                ->findByColumn(request('_parent_field'));

            $fields = $parent instanceof ModelRelationField
                ? $parent->getResource()?->getFormFields()
                : $parent->getFields();
        }

        if(is_null($fields)) {
            return $this->field;
        }

        $this->field = $fields
            ->onlyFields()
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

        /* @var ModelResource $resource */
        $resource = $this->getResource();

        $fields = match ($this->getPage()->pageType()) {
            PageType::INDEX => $resource->getIndexFields(),
            PageType::DETAIL => $resource->getDetailFields(withOutside: true),
            PageType::FORM => $resource->getFormFields(withOutside: true),
            default => Fields::make($resource->fields())
        };

        $this->field = $fields
            ->onlyFields()
            ->findByRelation($this->getRelationName());

        return $this->field;
    }

    /**
     * @throws Throwable
     */
    public function getFieldItemOrFail(): Model
    {
        $field = $this->getField();

        throw_if(is_null($field), FieldException::notFound());

        /* @var ModelResource $resource */
        $resource = $field->getResource();

        return $resource
            ->getModel()
            ->newModelQuery()
            ->findOrFail(request($resource->getModel()->getKeyName()));
    }
}
