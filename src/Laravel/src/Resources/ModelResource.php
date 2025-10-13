<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Resources;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Leeto\FastAttributes\Attributes;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\TypeCasts\DataCasterContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Core\Exceptions\ResourceException;
use MoonShine\Crud\Attributes\DestroyHandler;
use MoonShine\Crud\Attributes\MassDestroyHandler;
use MoonShine\Crud\Attributes\SaveHandler;
use MoonShine\Crud\Concerns\Resource\HasFilters;
use MoonShine\Crud\Contracts\Fields\HasOutsideSwitcherContract;
use MoonShine\Crud\Contracts\Page\DetailPageContract;
use MoonShine\Crud\Contracts\Page\FormPageContract;
use MoonShine\Crud\Contracts\Page\IndexPageContract;
use MoonShine\Crud\Contracts\Resource\WithQueryBuilderContract;
use MoonShine\Crud\Resources\CrudResource;
use MoonShine\Crud\Traits\Resource\ResourceWithFields;
use MoonShine\Laravel\Applies\FieldsWithoutFilters;
use MoonShine\Laravel\Collections\Fields;
use MoonShine\Laravel\DependencyInjection\MoonShine;
use MoonShine\Laravel\Fields\Relationships\ModelRelationField;
use MoonShine\Laravel\MoonShineAuth;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Laravel\Traits\Resource\ResourceModelQuery;
use MoonShine\Laravel\TypeCasts\ModelCaster;
use MoonShine\Support\Enums\Ability;
use MoonShine\UI\Fields\Field;
use Throwable;

/**
 * @template TData of Model
 * @template-covariant TIndexPage of null|IndexPageContract = null
 * @template-covariant TFormPage of null|FormPageContract = null
 * @template-covariant TDetailPage of null|DetailPageContract = null
 *
 * @extends CrudResource<MoonShine, TData, TIndexPage, TFormPage, TDetailPage, ModelNotFoundException<TData>, Fields>
 * @implements WithQueryBuilderContract<Builder>
 *
 * @use ResourceWithFields<Fields>
 * @use HasFilters<Fields>
 */
abstract class ModelResource extends CrudResource implements WithQueryBuilderContract
{
    /**
     * @use ResourceModelQuery<TData>
     */
    use ResourceModelQuery;

    /** @var class-string<TData> */
    protected string $model;

    protected string $column = '';

    /**
     * @deprecated Will be removed in 5.0
     * @return list<class-string<FieldContract>>
     * @see IndexPage
     */
    protected function getIgnoredFields(): array
    {
        return FieldsWithoutFilters::LIST;
    }

    public function flushState(): void
    {
        parent::flushState();

        $this->queryBuilder = null;
        $this->customQueryBuilder = null;
    }

    public function getColumn(): string
    {
        return $this->column ?: $this->getModel()->getKeyName();
    }

    /**
     * @return TData
     */
    public function getModel(): Model
    {
        return new $this->model();
    }

    /**
     * @return TData
     */
    public function getDataInstance(): mixed
    {
        return $this->getModel();
    }

    /**
     * @return ModelCaster<TData>
     */
    public function getCaster(): DataCasterContract
    {
        /** @noRector */
        return new ModelCaster($this->model);
    }

    protected function isCan(Ability $ability): bool
    {
        $user = MoonShineAuth::getGuard()->user();

        if ($user === null) {
            return true;
        }

        $item = \in_array($ability, [
            Ability::CREATE,
            Ability::MASS_DELETE,
            Ability::VIEW_ANY,
        ], true)
            ? $this->getDataInstance()
            : $this->getItem();

        $checkCustomRules = moonshineConfig()
            ->getAuthorizationRules()
            ->every(fn ($rule) => $rule($this, $user, $ability, $item));

        if (! $checkCustomRules) {
            return false;
        }

        if (! $this->isWithPolicy()) {
            return true;
        }

        return Gate::forUser($user)->allows($ability->value, $item);
    }

    /**
     * @param  array<int|string>  $ids
     */
    public function massDelete(array $ids): void
    {
        if ($handler = Attributes::for($this, MassDestroyHandler::class)->first()) {
            $service = $this->getCore()->getContainer($handler->service);

            $handler->method === null
                ? $service($ids)
                : $service->{$handler->method}($ids);

            return;
        }

        $this->beforeMassDeleting($ids);

        $this->getDataInstance()
            ->newModelQuery()
            ->whereIn($this->getDataInstance()->getKeyName(), $ids)
            ->get()
            ->each(fn (Model $item): bool => $this->delete($this->getCaster()->cast($item)));

        $this->afterMassDeleted($ids);
    }

    /**
     * @param DataWrapperContract<TData> $item
     * @param ?Fields $fields
     * @throws Throwable
     */
    public function delete(DataWrapperContract $item, ?FieldsContract $fields = null): bool
    {
        $fields ??= $this->getFormFields()->onlyFields(withApplyWrappers: true);

        $fields->fill($item->toArray(), $item);

        if ($handler = Attributes::for($this, DestroyHandler::class)->first()) {
            $service = $this->getCore()->getContainer($handler->service);

            return $handler->method === null
                ? $service($item->getOriginal())
                : $service->{$handler->method}($item->getOriginal());
        }

        $item = $this->beforeDeleting($item);

        $relationDestroyer = static function (ModelRelationField $field) use ($item): void {
            $relationItems = $item->{$field->getRelationName()};

            ! $field->isToOne() ?: $relationItems = new Collection([$relationItems]);

            $relationItems->each(
                static fn (mixed $relationItem): mixed => $field->afterDestroy($relationItem),
            );
        };

        $fields->each(function (FieldContract $field) use ($item, $relationDestroyer): void {
            if ($field instanceof ModelRelationField
                && $field instanceof HasOutsideSwitcherContract
                && ! $field->isOutsideComponent()
                && $this->isDeleteRelationships()
            ) {
                $relationDestroyer($field);
            } else {
                $field->afterDestroy($item);
            }
        });

        if ($this->isDeleteRelationships()) {
            /** @var Fields<ModelRelationField> $outsideCollection */
            $outsideCollection = $this->getOutsideFields();
            $outsideCollection->each($relationDestroyer);
        }

        return (bool) tap($item->getOriginal()->delete(), fn (): DataWrapperContract => $this->afterDeleted($item));
    }

    /**
     * @param DataWrapperContract<TData> $item
     * @param ?Fields $fields
     * @return DataWrapperContract<TData>
     *
     * @throws ResourceException
     * @throws Throwable
     */
    public function save(DataWrapperContract $item, ?FieldsContract $fields = null): DataWrapperContract
    {
        $fields ??= $this->getFormFields()->onlyFields(withApplyWrappers: true);

        $fields->fill($item->toArray(), $item);

        if ($handler = Attributes::for($this, SaveHandler::class)->first()) {
            $result = $this->resolveSaveHandler($handler, $item, $fields);
            $this->setItem($result);

            return $this->getCastedData();
        }

        try {
            $fields->each(static fn (FieldContract $field): mixed => $field->beforeApply($item->getOriginal()));

            if ($item->getKey() === null) {
                $item = $this->beforeCreating($item);
            }

            if ($item->getKey() !== null) {
                $item = $this->beforeUpdating($item);
            }

            $fields->withoutOutside()
                ->each(fn (FieldContract $field): mixed => $field->apply($this->fieldApply($field), $item->getOriginal()));

            if ($item->getOriginal()->save()) {
                $this->isRecentlyCreated = $item->getOriginal()->wasRecentlyCreated;

                $item = $this->afterSave($item, $fields);
            }
        } catch (QueryException $queryException) {
            throw new ResourceException($queryException->getMessage(), previous: $queryException);
        }

        $this->setItem($item->getOriginal());

        return $item;
    }

    /**
     * @param DataWrapperContract<TData> $item
     * @return TData
     */
    private function resolveSaveHandler(SaveHandler $handler, DataWrapperContract $item, FieldsContract $fields): Model
    {
        $service = $this->getCore()->getContainer($handler->service);
        $resource = $this;

        $initial = clone $item;
        $data = Field::silentApply(static function () use ($item, $fields, $resource): array {
            $fields->each(static fn (FieldContract $field): mixed => $field->beforeApply($item->getOriginal()));
            $fields->each(static fn (FieldContract $field): mixed => $field->apply($resource->fieldApply($field), $item->getOriginal()));
            $fields->each(static fn (FieldContract $field): mixed => $field->afterApply($item->getOriginal()));

            return $item->toArray();
        });

        return $handler->method === null
            ? $service($initial->getOriginal(), $data)
            : $service->{$handler->method}($initial->getOriginal(), $data);
    }

    public function fieldApply(FieldContract $field): Closure
    {
        /**
         * @param TData $item
         * @return TData
         */
        return static function (mixed $item) use ($field): mixed {
            if (! $field->hasRequestValue() && ! $field->getDefaultIfExists()) {
                return $item;
            }

            $value = $field->getRequestValue() !== false ? $field->getRequestValue() : null;

            data_set($item, $field->getColumn(), $value);

            return $item;
        };
    }

    /**
     * @param DataWrapperContract<TData> $item
     * @param Fields $fields
     * @return DataWrapperContract<TData>
     */
    protected function afterSave(DataWrapperContract $item, FieldsContract $fields): DataWrapperContract
    {
        $wasRecentlyCreated = $this->isRecentlyCreated();

        $fields->each(static fn (FieldContract $field): mixed => $field->afterApply($item->getOriginal()));

        if ($item->getOriginal()->isDirty()) {
            $item->getOriginal()->save();
        }

        if ($wasRecentlyCreated) {
            $item = $this->afterCreated($item);
        }

        if (! $wasRecentlyCreated) {
            $item = $this->afterUpdated($item);
        }

        return $item;
    }

    /**
     * @return string[]
     */
    protected function search(): array
    {
        return [
            $this->getModel()->getKeyName(),
        ];
    }
}
