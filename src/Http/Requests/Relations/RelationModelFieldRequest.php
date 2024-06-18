<?php

declare(strict_types=1);

namespace MoonShine\Http\Requests\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Enums\PageType;
use MoonShine\Exceptions\FieldException;
use MoonShine\Fields\Fields;
use MoonShine\Fields\Relationships\ModelRelationField;
use MoonShine\Traits\Request\HasPageRequest;
use MoonShine\Traits\Request\HasResourceRequest;
use Throwable;

class RelationModelFieldRequest extends FormRequest
{
    use HasResourceRequest;
    use HasPageRequest;

    public function getRelationName(): string
    {
        return request()->input('_relation');
    }

    /**
     * @throws Throwable
     */
    public function getPageField(): ?ModelRelationField
    {
        return memoize(function () {
            $fields = $this->getPage()->getComponents();

            if($parentField = request()->input('_parent_field')) {
                /** @var HasFields $parent */
                $parent = $fields
                    ->onlyFields()
                    ->onlyHasFields()
                    ->findByColumn(
                        $parentField
                    );

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
            /* @var \MoonShine\Resources\ModelResource $resource */
            $resource = $this->getResource();

            $fields = match ($this->getPage()->pageType()) {
                PageType::INDEX => $resource->getIndexFields(),
                PageType::DETAIL => $resource->getDetailFields(withOutside: true),
                PageType::FORM => $resource->getFormFields(withOutside: true),
                default => Fields::make($resource->fields())
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

        /* @var \MoonShine\Resources\ModelResource $resource */
        $resource = $field->getResource();

        return $resource
            ->getModel()
            ->newModelQuery()
            ->findOrFail(
                request()->input($resource->getModel()->getKeyName())
            );
    }
}
