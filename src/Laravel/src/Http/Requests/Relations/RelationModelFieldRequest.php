<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Requests\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use MoonShine\Contracts\Core\CrudPageContract;
use MoonShine\Contracts\Core\CrudResourceContract;
use MoonShine\Contracts\UI\HasFieldsContract;
use MoonShine\Laravel\Collections\Fields;
use MoonShine\Laravel\DependencyInjection\MoonShine;
use MoonShine\Laravel\Fields\Relationships\ModelRelationField;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Laravel\Traits\Request\HasPageRequest;
use MoonShine\Laravel\Traits\Request\HasResourceRequest;
use MoonShine\Support\Enums\PageType;
use MoonShine\UI\Exceptions\FieldException;
use Throwable;

class RelationModelFieldRequest extends FormRequest
{
    /** @use HasResourceRequest<CrudResourceContract> */
    use HasResourceRequest;
    /** @use HasPageRequest<CrudPageContract<ModelResource, MoonShine, Fields>> */
    use HasPageRequest;

    public function getRelationName(): string
    {
        return request()->getScalar('_relation', '');
    }

    /**
     * @throws Throwable
     */
    public function getPageField(): ?ModelRelationField
    {
        return memoize(function () {
            /**
             * @var Fields $fields
             * @phpstan-ignore-next-line
             */
            $fields = $this->getPage()->getComponents();

            if ($parentField = request()->getScalar('_parent_field')) {
                /** @var HasFieldsContract<Fields> $parent */
                $parent = $fields
                    ->onlyFields()
                    ->onlyHasFields()
                    ->findByColumn($parentField);

                /**
                 * @var Fields $fields
                 */
                $fields = $parent instanceof ModelRelationField
                    ? $parent->getResource()?->getFormFields()
                    : $parent->getFields();
            }

            if (\is_null($fields)) {
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

            /* @var Fields $fields */
            $fields = $fields->onlyFields();

            /** @phpstan-ignore-next-line  */
            return $fields->findByRelation($this->getRelationName());
        });
    }

    /**
     * @throws Throwable
     */
    public function getFieldItemOrFail(): Model
    {
        $field = $this->getField();

        if (\is_null($field)) {
            throw FieldException::notFound();
        }

        /* @var \MoonShine\Laravel\Resources\ModelResource $resource */
        $resource = $field->getResource();

        return $resource
            ->getDataInstance()
            ->newModelQuery()
            ->findOrFail(
                request()->getScalar($resource->getDataInstance()->getKeyName())
            );
    }
}
