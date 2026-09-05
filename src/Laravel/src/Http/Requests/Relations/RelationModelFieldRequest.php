<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Requests\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use MoonShine\Contracts\Core\CrudPageContract;
use MoonShine\Contracts\Core\CrudResourceContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\HasFieldsContract;
use MoonShine\Core\Exceptions\ResourceException;
use MoonShine\Laravel\Collections\Fields;
use MoonShine\Laravel\DependencyInjection\MoonShine;
use MoonShine\Laravel\Fields\Relationships\ModelRelationField;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Laravel\Traits\Request\HasPageRequest;
use MoonShine\Laravel\Traits\Request\HasResourceRequest;
use MoonShine\Support\Enums\Ability;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\Enums\PageType;
use MoonShine\UI\Exceptions\FieldException;
use Throwable;

class RelationModelFieldRequest extends FormRequest
{
    /** @use HasResourceRequest<CrudResourceContract> */
    use HasResourceRequest;
    /** @use HasPageRequest<CrudPageContract<ModelResource, MoonShine, Fields>> */
    use HasPageRequest;

    /**
     * @throws Throwable
     */
    public function authorize(): bool
    {
        $resource = $this->getResource();

        if (\is_null($resource)) {
            throw ResourceException::notDeclared();
        }

        $ability = match ($this->getPage()->getPageType()) {
            PageType::INDEX => Ability::VIEW_ANY,
            PageType::DETAIL => Ability::VIEW,
            PageType::FORM => $this->route('resourceItem') === null
                ? Ability::CREATE
                : Ability::UPDATE,
            default => Ability::VIEW_ANY,
        };

        $action = match ($ability) {
            Ability::VIEW => Action::VIEW,
            Ability::CREATE => Action::CREATE,
            Ability::UPDATE => Action::UPDATE,
            default => null,
        };

        if ($action !== null && ! $resource->hasAction($action)) {
            return false;
        }

        return $resource->can($ability);
    }

    public function getRelationName(): string
    {
        return request()->getScalar('_relation', '');
    }

    /**
     * @param class-string<ModelRelationField>|null $fieldClass
     * @throws Throwable
     */
    public function getPageField(?string $fieldClass = null): ?ModelRelationField
    {
        return memoize(function () use ($fieldClass) {
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
                $fields = $parent->getFields();
            }

            if (\is_null($fields)) {
                return null;
            }

            return $fields
                ->onlyFields()
                ->filter(static fn (FieldContract $field): bool => $fieldClass === null || $field instanceof $fieldClass)
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
