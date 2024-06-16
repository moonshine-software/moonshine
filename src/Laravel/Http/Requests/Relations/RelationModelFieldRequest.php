<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Requests\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use MoonShine\Laravel\Fields\Relationships\ModelRelationField;
use MoonShine\Laravel\Traits\Request\HasPageRequest;
use MoonShine\Laravel\Traits\Request\HasResourceRequest;
use MoonShine\Support\Enums\PageType;
use MoonShine\UI\Contracts\Fields\HasFields;
use MoonShine\UI\Exceptions\FieldException;
use Throwable;

class RelationModelFieldRequest extends FormRequest
{
    use HasResourceRequest;
    use HasPageRequest;

    public function getRelationName(): string
    {
        return request('_relation');
    }

    /**
     * @throws Throwable
     */
    public function getPageField(): ?ModelRelationField
    {
        return memoize(function () {
            $fields = $this->getPage()->getComponents();

            if(request('_parent_field')) {
                /** @var HasFields $parent */
                $parent = $fields
                    ->onlyFields()
                    ->onlyHasFields()
                    ->findByColumn(request('_parent_field'));

                $fields = $parent instanceof ModelRelationField
                    ? $parent->getResource()?->getFormFields()
                    : $parent->getFields();
            }

            if(is_null($fields)) {
                return null;
            }

            return $fields
                ->onlyFields()
                ->findByRelation($this->getRelationName());
        });
    }

    /**
     * @throws Throwable
     */
    public function getField(): ?ModelRelationField
    {
        return memoize(function (): ?ModelRelationField {
            /* @var \MoonShine\Laravel\Resources\ModelResource $resource */
            $resource = $this->getResource();

            $fields = match ($this->getPage()->getPageType()) {
                PageType::INDEX => $resource->getIndexFields(),
                PageType::DETAIL => $resource->getDetailFields(withOutside: true),
                PageType::FORM => $resource->getFormFields(withOutside: true),
                default => $resource->getFormFields()
            };

            return $fields
                ->onlyFields()
                ->findByRelation($this->getRelationName());
        });
    }

    /**
     * @throws Throwable
     */
    public function getFieldItemOrFail(): Model
    {
        $field = $this->getField();

        throw_if(is_null($field), FieldException::notFound());

        /* @var \MoonShine\Laravel\Resources\ModelResource $resource */
        $resource = $field->getResource();

        return $resource
            ->getModel()
            ->newModelQuery()
            ->findOrFail(request($resource->getModel()->getKeyName()));
    }
}
