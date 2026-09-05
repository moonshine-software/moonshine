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
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\TypeCasts\DataCasterContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Core\Exceptions\ResourceException;
use MoonShine\Crud\Attributes\DestroyHandler;
use MoonShine\Crud\Attributes\MassDestroyHandler;
use MoonShine\Crud\Attributes\SaveHandler;
use MoonShine\Crud\Contracts\Fields\HasOutsideSwitcherContract;
use MoonShine\Crud\Contracts\Page\DetailPageContract;
use MoonShine\Crud\Contracts\Page\FormPageContract;
use MoonShine\Crud\Contracts\Page\IndexPageContract;
use MoonShine\Crud\Contracts\Resource\WithQueryBuilderContract;
use MoonShine\Crud\Resources\CrudResource;
use MoonShine\Crud\Traits\Resource\ResourceWithFields;
use MoonShine\Laravel\Collections\Fields;
use MoonShine\Laravel\DependencyInjection\MoonShine;
use MoonShine\Laravel\Fields\Relationships\ModelRelationField;
use MoonShine\Laravel\MoonShineAuth;
use MoonShine\Laravel\Traits\Resource\ResourceModelQuery;
use MoonShine\Laravel\TypeCasts\ModelCaster;
use MoonShine\Support\Enums\Ability;
use MoonShine\UI\Fields\Field;
use Throwable;

/**
 * @template TData of Model = Model
 * @template-covariant TIndexPage of null|IndexPageContract = IndexPageContract
 * @template-covariant TFormPage of null|FormPageContract = FormPageContract
 * @template-covariant TDetailPage of null|DetailPageContract = DetailPageContract
 *
 * @extends CrudResource<MoonShine, TData, TIndexPage, TFormPage, TDetailPage, ModelNotFoundException<TData>, Fields>
 * @implements WithQueryBuilderContract<Builder<TData>|\Illuminate\Database\Eloquent\Relations\Relation<TData, Model, mixed>>
 *
 * @use ResourceWithFields<Fields>
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
     * @return TIndexPage|null
     */
    public function getIndexPage(): ?PageContract
    {
        return parent::getIndexPage();
    }

    /**
     * @return TFormPage|null
     */
    public function getFormPage(): ?PageContract
    {
        return parent::getFormPage();
    }

    /**
     * @return TDetailPage|null
     */
    public function getDetailPage(): ?PageContract
    {
        return parent::getDetailPage();
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
            ->every(fn (Closure $rule): bool => $rule($this, $user, $ability, $item));

        if (! $checkCustomRules) {
            return false;
        }

        if (! $this->isWithPolicy()) {
            return true;
        }

        return Gate::forUser($user)->allows($ability->value, $item);
    }

    protected function getQueryCacheKeySuffix(): string
    {
        $identifier = MoonShineAuth::getGuard()->id() ?? 'guest';

        return '_user_' . str_replace(['\\', '/', ':'], '_', (string) $identifier);
    }

    /**
     * @param  array<int|string>  $ids
     */
    public function massDelete(array $ids): void
    {
        $handler = Attributes::for($this, MassDestroyHandler::class)->first();

        if ($handler instanceof MassDestroyHandler) {
            $service = $this->getCore()->getContainer($handler->service);

            /** @var callable(array<array-key, int|string>): void $callback */
            $callback = $handler->method === null ? $service : [$service, $handler->method];
            $callback($ids);

            return;
        }

        $this->beforeMassDeleting($ids);

        /** @var Builder<TData> $query */
        $query = $this->getDataInstance()->newModelQuery();
        /** @var \Illuminate\Database\Eloquent\Collection<int, TData> $items */
        $items = $query
            ->whereIn($this->getDataInstance()->getKeyName(), $ids)
            ->get();
        $items->each(fn ($item): bool => $this->delete($this->getCaster()->cast($item)));

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

        $handler = Attributes::for($this, DestroyHandler::class)->first();

        if ($handler instanceof DestroyHandler) {
            $service = $this->getCore()->getContainer($handler->service);

            /** @var callable(TData): bool $callback */
            $callback = $handler->method === null ? $service : [$service, $handler->method];

            return $callback($item->getOriginal());
        }

        $item = $this->beforeDeleting($item);

        $relationDestroyer = static function (ModelRelationField $field) use ($item): void {
            $relationItems = $item->{$field->getRelationName()};

            ! $field->isToOne() ?: $relationItems = new Collection([$relationItems]);

            Collection::wrap($relationItems)->each(
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

        $handler = Attributes::for($this, SaveHandler::class)->first();

        if ($handler instanceof SaveHandler) {
            $result = $this->resolveSaveHandler($handler, $item, $fields);
            $this->setItem($result);

            return $this->getCastedData() ?? $this->getCaster()->cast($result);
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

        /** @var callable(TData, array<string, mixed>): TData $callback */
        $callback = $handler->method === null ? $service : [$service, $handler->method];

        return $callback($initial->getOriginal(), $data);
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
            return $this->afterUpdated($item);
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
