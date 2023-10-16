<?php

declare(strict_types=1);

namespace MoonShine\Http\Requests\Relations;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\Fields\HasFields;
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
    public function getComponentField(): ?ModelRelationField
    {
        if (! is_null($this->field)) {
            return $this->field;
        }

        $fields = $this->getPageComponent(request('_component_name'))
            ?->getFields($this->getPage()->uriKey());

        if(is_null($fields)) {
            return $this->field;
        }

        $fields->each(function ($field) use($fields): void {
            if (! $field instanceof HasFields) {
                return;
            }

            $field->preparedFields();

            if ($field->hasFields()) {
                $field->getFields()->each(fn ($nestedField) => $fields->add($nestedField));
            }
        });

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

        $fields = match ($this->getPage()::class) {
            IndexPage::class => $resource->getIndexFields(),
            DetailPage::class => $resource->getDetailFields(),
            FormPage::class => Fields::make(
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
