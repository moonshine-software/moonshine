<?php

declare(strict_types=1);

namespace MoonShine\Http\Requests\Relations;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Enums\PageType;
use MoonShine\Fields\Field;
use MoonShine\Fields\Fields;
use MoonShine\Fields\Relationships\ModelRelationField;
use MoonShine\Http\Requests\MoonshineFormRequest;
use MoonShine\Pages\Crud\DetailPage;
use MoonShine\Pages\Crud\FormPage;
use MoonShine\Pages\Crud\IndexPage;
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
    public function getPageField(): ?ModelRelationField
    {
        if (! is_null($this->field)) {
            return $this->field;
        }

        $fields = request('_parent_field')
            ? $this->getPage()->getComponents()->onlyFields()
                ->filter(static fn (Field $field): bool => $field instanceof HasFields)
                ->findByColumn(request('_parent_field'))
                ?->preparedFields()
            : $this->getPage()->getComponents()->onlyFields();

        if(is_null($fields)) {
            return $this->field;
        }

        $this->field = $fields
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

        $fields = match ($this->getPage()->pageType()) {
            PageType::INDEX => $resource->getIndexFields(),
            PageType::DETAIL => $resource->getDetailFields(),
            PageType::FORM => Fields::make(
                empty($resource->formFields())
                    ? $resource->fields()
                    : $resource->formFields()
            )->filter()->formFields(),
            default => Fields::make($resource->fields())
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
