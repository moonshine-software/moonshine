<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Resource;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ApplyContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Core\Exceptions\ResourceException;
use MoonShine\Crud\Contracts\HasQueryTagsContract;
use MoonShine\Crud\Exceptions\CrudResourceException;
use MoonShine\Crud\QueryTags\QueryTag;
use MoonShine\Crud\Traits\Resource\ResourceQuery;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Laravel\Support\DBOperators;
use Throwable;

/**
 * @template T of Model
 * @mixin ResourceQuery
 */
trait ResourceModelQuery
{
    /**
     * @var string[]
     */
    protected array $with = [];

    /** @var EloquentBuilder<T>|Relation<T, Model, mixed>|null */
    protected ?Builder $queryBuilder = null;

    /** @var EloquentBuilder<T>|Relation<T, Model, mixed>|null */
    protected ?Builder $customQueryBuilder = null;

    protected bool $disableQueryFeatures = false;

    /**
     * @return iterable<array-key, T>
     * @throws Throwable
     */
    public function getItems(): iterable|Collection|LazyCollection|CursorPaginator|Paginator
    {
        /** @var iterable<array-key, T> */
        return $this->isPaginationUsed()
            ? $this->paginate()
            : $this->getQuery()->get();
    }

    /**
     * @throws Throwable
     * @return (Paginator<array-key, T>|CursorPaginator<array-key, T>)&iterable<array-key, T>
     */
    protected function paginate(): Paginator|CursorPaginator
    {
        $query = $this->getQuery();

        if ($this->isCursorPaginate()) {
            $paginate = $query->cursorPaginate(
                $this->getItemsPerPage(),
                cursorName: $this->getUriKey(),
            );
        } elseif ($this->isSimplePaginate()) {
            $paginate = $query->simplePaginate(
                $this->getItemsPerPage(),
                pageName: $this->getQueryParamName('page'),
                page: $this->getPaginatorPage()
            );
        } else {
            $paginate = $query->paginate(
                $this->getItemsPerPage(),
                pageName: $this->getQueryParamName('page'),
                page: $this->getPaginatorPage(),
            );
        }

        $params = $this->getQueryParams()->except($this->getQueryParamName('page'))->toArray();

        /** @var (Paginator<array-key, T>|CursorPaginator<array-key, T>)&iterable<array-key, T> */
        return $paginate->appends($params);
    }

    /**
     * @param  bool  $orFail
     *
     * @return ($orFail is true ? DataWrapperContract<T> : DataWrapperContract<T>|null)
     * @throws ModelNotFoundException<T>
     */
    public function findItem(bool $orFail = false): ?DataWrapperContract
    {
        /** @var EloquentBuilder<T> $query */
        $query = $this->getModel()->newQuery();
        $builder = $this->modifyItemQueryBuilder($query);

        if ($orFail) {
            /** @var T $item */
            $item = $builder->findOrFail($this->getItemID());

            return $this->getCaster()->cast($item);
        }

        /** @var T|null $item */
        $item = $builder->find($this->getItemID());

        return $item !== null ? $this->getCaster()->cast($item) : null;
    }

    /**
     * @param EloquentBuilder<T>|Relation<T, Model, mixed> $builder
     * @return EloquentBuilder<T>|Relation<T, Model, mixed>
     */
    protected function modifyItemQueryBuilder(Builder $builder): Builder
    {
        return $builder;
    }

    /**
     * @throws Throwable
     * @return EloquentBuilder<T>|Relation<T, Model, mixed>
     */
    public function newQuery(): mixed
    {
        if (! \is_null($this->queryBuilder)) {
            return $this->queryBuilder;
        }

        /** @var EloquentBuilder<T>|Relation<T, Model, mixed> $query */
        $query = $this->customQueryBuilder ?? $this->getModel()->newQuery();
        $this->queryBuilder = $query;

        if ($this->hasWith()) {
            $this->queryBuilder->with($this->getWith());
        }

        return $this->queryBuilder = $this->modifyQueryBuilder($this->queryBuilder);
    }

    /**
     * @throws Throwable
     * @return EloquentBuilder<T>|Relation<T, Model, mixed>
     */
    public function getQuery(): mixed
    {
        $this->queryBuilderFeatures();

        return $this->newQuery();
    }

    /**
     * @param EloquentBuilder<T>|Relation<T, Model, mixed> $builder
     * @return EloquentBuilder<T>|Relation<T, Model, mixed>
     */
    protected function modifyQueryBuilder(Builder $builder): Builder
    {
        return $builder;
    }

    /**
     * @param EloquentBuilder<T>|Relation<T, Model, mixed> $builder
     *
     */
    public function customQueryBuilder(mixed $builder): static
    {
        $this->customQueryBuilder = $builder;

        return $this;
    }

    /**
     * @throws Throwable
     */
    protected function queryBuilderFeatures(): void
    {
        if ($this->isDisabledQueryFeatures()) {
            return;
        }

        $this
            ->withCache()
            ->withTags()
            ->withSearch($this->getSearchQueryKey())
            ->withFilters()
            ->withParentResource()
            ->withOrder()
            ->withCachedQueryParams();
    }

    public function isDisabledQueryFeatures(): bool
    {
        return $this->disableQueryFeatures;
    }

    public function disableQueryFeatures(): static
    {
        $this->disableQueryFeatures = true;

        return $this;
    }

    public function isItemExists(): bool
    {
        return ! \is_null($this->getCastedData()?->getKey());
    }

    public function hasWith(): bool
    {
        return $this->with !== [];
    }

    /**
     * @return string[]
     */
    public function getWith(): array
    {
        return $this->with;
    }

    public function getSortColumn(): string
    {
        return $this->sortColumn ?: $this->getModel()->getKeyName();
    }

    /**
     * @throws Throwable
     */
    protected function withTags(): static
    {
        $page = $this->getIndexPage();

        if (! $page instanceof HasQueryTagsContract) {
            return $this;
        }

        if (! $page->hasQueryTags()) {
            return $this;
        }

        /** @var QueryTag<EloquentBuilder<T>|Relation<T, Model, mixed>>|null $tag */
        $tag = Collection::make($page->getQueryTags())
            ->first(
                static fn (QueryTag $tag): bool => $tag->isActive(),
            );

        if ($tag) {
            $this->customQueryBuilder(
                $tag->apply(
                    $this->newQuery(),
                ),
            );
        }

        return $this;
    }

    protected function resolveSearch(string $terms, ?iterable $fullTextColumns = null): static
    {
        if (! \is_null($fullTextColumns)) {
            $this->newQuery()->whereFullText(iterator_to_array($fullTextColumns), $terms);
        } else {
            $this->searchQuery($terms);
        }

        return $this;
    }

    protected function searchQuery(string $terms): void
    {
        $this->newQuery()->where(function (Builder $builder) use ($terms): void {
            foreach ($this->getSearchColumns() as $key => $column) {
                if (Str::of($column)->contains('.')) {
                    $column = Str::of($column)
                        ->explode('.')
                        ->tap(static function (Collection $data) use (&$key): void {
                            $key = $data->first();
                        })
                        ->slice(-1)
                        ->values()
                        ->all();
                }

                if (\is_array($column)) {
                    $builder->when(
                        method_exists($this->getDataInstance(), $key),
                        static fn (Builder $query) => $query->orWhereHas(
                            $key,
                            static fn (Builder $q) => Collection::make($column)->each(static fn ($item) => $q->where(
                                static fn (Builder $qq) => $qq->orWhere(
                                    $item,
                                    DBOperators::byModel($qq->getModel())->like(),
                                    "%$terms%",
                                ),
                            )),
                        ),
                        static fn (Builder $query) => Collection::make($column)->each(static fn ($item) => $query->orWhere(
                            static function (Builder $qq) use ($key, $item, $terms): void {
                                $qq->orWhereJsonContains($key, [$item => $terms]);
                            },
                        )),
                    );
                } else {
                    $builder->orWhere($column, DBOperators::byModel($builder->getModel())->like(), "%$terms%");
                }
            }
        });
    }

    /**
     * @throws Throwable
     */
    protected function withFilters(): static
    {
        $filters = $this->prepareFilters();

        if (\is_null($filters)) {
            return $this;
        }

        $filters->each(function (FieldContract $filter): void {
            if ($this->isSaveQueryState()) {
                $filter->onRequestValue(
                    static fn (mixed $value): mixed => $value === false ? $filter->getValue() : $value
                );
            }

            if ($filter->getRequestValue() === false) {
                return;
            }

            $filterApply = $filter->getApplyClass('filters', ModelResource::class);

            $defaultApply = static fn (Builder $query): Builder => $query->where(
                $filter->getColumn(),
                $filter->getRequestValue(),
            );

            if ($filterApply instanceof ApplyContract) {
                $filter->onApply($filterApply->apply($filter));
            } elseif (! $filter->hasOnApply()) {
                $filter->onApply($defaultApply);
            }

            $filter->apply(
                $defaultApply,
                $this->newQuery(),
            );
        });

        return $this;
    }

    /**
     * @throws ResourceException
     */
    protected function withParentResource(): static
    {
        $relationName = $this->getCore()->getCrudRequest()->getParentRelationName();
        $parentId = $this->getCore()->getCrudRequest()->getParentRelationId();

        if (\is_null($relationName) || \is_null($parentId)) {
            return $this;
        }

        if (! method_exists($this->getDataInstance(), $relationName)) {
            throw CrudResourceException::relationNotFound($relationName);
        }

        /** @var BelongsToMany<Model, Model>|\Illuminate\Database\Eloquent\Relations\BelongsTo<Model, Model>|\Illuminate\Database\Eloquent\Relations\HasOneOrMany<Model, Model, mixed> $relation */
        $relation = $this->getDataInstance()->{$relationName}();

        if ($relation instanceof BelongsToMany) {
            $this->newQuery()->whereRelation(
                $relationName,
                $relation->getQualifiedRelatedKeyName(),
                $parentId,
            );
        } else {
            $this->newQuery()->where(
                $relation->getForeignKeyName(),
                $parentId,
            );
        }

        return $this;
    }

    /**
     * @throws Throwable
     */
    protected function withOrder(): static
    {
        [$column, $direction, $callback] = $this->prepareOrder();

        return $this->resolveOrder($column, $direction, $callback);
    }

    /**
     * @throws Throwable
     * @param 'asc'|'desc' $direction
     */
    protected function resolveOrder(string $column, string $direction, ?Closure $callback): static
    {
        if ($callback instanceof Closure) {
            $callback($this->newQuery(), $column, $direction);
        } else {
            $this->newQuery()->orderBy($column, $direction);
        }

        return $this;
    }
}
