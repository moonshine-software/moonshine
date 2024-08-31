<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Resource;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use MoonShine\Contracts\UI\ApplyContract;
use MoonShine\Core\Exceptions\ResourceException;
use MoonShine\Laravel\QueryTags\QueryTag;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Laravel\Support\DBOperators;
use MoonShine\UI\Fields\Field;
use Throwable;

/**
 * @template-covariant T
 */
trait ResourceModelQuery
{
    protected array $with = [];

    protected array $parentRelations = [];

    protected ?Builder $queryBuilder = null;

    protected ?Builder $customQueryBuilder = null;

    /**
     * @throws Throwable
     */
    public function getItems(): Collection|Paginator
    {
        return $this->isPaginationUsed()
            ? $this->paginate()
            : $this->getQuery()->get();
    }

    /**
     * @throws Throwable
     */
    protected function paginate(): Paginator
    {
        return $this->getQuery()
            ->when(
                $this->isSimplePaginate(),
                fn (Builder $query): Paginator => $query->simplePaginate(
                    $this->getItemsPerPage(),
                    page: $this->getPaginatorPage()
                ),
                fn (Builder $query): LengthAwarePaginator => $query->paginate(
                    $this->getItemsPerPage(),
                    page: $this->getPaginatorPage()
                ),
            )
            ->appends($this->getQueryParams()->except('page')->toArray());
    }

    /**
     * @return T
     */
    public function findItem(bool $orFail = false): mixed
    {
        $builder = $this->modifyItemQueryBuilder(
            $this->getModel()->newQuery()
        );

        if($orFail) {
            return $builder->findOrFail($this->getItemID());
        }

        return $builder->find($this->getItemID());
    }

    protected function modifyItemQueryBuilder(Builder $builder): Builder
    {
        return $builder;
    }

    /**
     * @throws Throwable
     */
    public function newQuery(): Builder
    {
        if (! is_null($this->queryBuilder)) {
            return $this->modifyQueryBuilder($this->queryBuilder);
        }

        $this->queryBuilder  = $this->customQueryBuilder  ?? $this->getModel()->newQuery();

        if ($this->hasWith()) {
            $this->queryBuilder->with($this->getWith());
        }

        return $this->modifyQueryBuilder($this->queryBuilder);
    }

    public function getQuery(): Builder
    {
        $this->queryBuilderFeatures();

        return $this->newQuery();
    }

    protected function modifyQueryBuilder(Builder $builder): Builder
    {
        return $builder;
    }

    public function customQueryBuilder(Builder $builder): static
    {
        $this->customQueryBuilder = $builder;

        return $this;
    }

    /**
     * @throws Throwable
     */
    protected function queryBuilderFeatures(): void
    {
        $this
            ->withCache()
            ->withTags()
            ->withSearch()
            ->withFilters()
            ->withParentResource()
            ->withOrder()
            ->withCachedQueryParams();
    }

    public function isItemExists(): bool
    {
        return !is_null($this->getItem()) && $this->getItem()->exists;
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
     * Get an array of custom form actions
     *
     * @return list<QueryTag>
     */
    protected function queryTags(): array
    {
        return [];
    }

    public function hasQueryTags(): bool
    {
        return $this->queryTags() !== [];
    }

    protected function withTags(): static
    {
        /** @var QueryTag $tag */
        $tag = collect($this->getQueryTags())
            ->first(
                static fn (QueryTag $tag): bool => $tag->isActive()
            );

        if ($tag) {
            $this->customQueryBuilder(
                $tag->apply(
                    $this->newQuery()
                )
            );
        }

        return $this;
    }

    protected function resolveSearch(string $terms, ?iterable $fullTextColumns = null): static
    {
        if (! is_null($fullTextColumns)) {
            $this->newQuery()->whereFullText($fullTextColumns, $terms);
        } else {
            $this->searchQuery($terms);
        }

        return $this;
    }

    protected function searchQuery(string $terms): void
    {
        $this->newQuery()->where(function (Builder $builder) use ($terms): void {
            foreach ($this->getSearchColumns() as $key => $column) {
                if (is_string($column) && str($column)->contains('.')) {
                    $column = str($column)
                        ->explode('.')
                        ->tap(static function (Collection $data) use (&$key): void {
                            $key = $data->first();
                        })
                        ->slice(-1)
                        ->values()
                        ->toArray();
                }

                if (is_array($column)) {
                    $builder->when(
                        method_exists($this->getDataInstance(), $key),
                        static fn (Builder $query) => $query->orWhereHas(
                            $key,
                            static fn (Builder $q) => collect($column)->each(static fn ($item) => $q->where(
                                static fn (Builder $qq) => $qq->orWhere(
                                    $item,
                                    DBOperators::byModel($qq->getModel())->like(),
                                    "%$terms%"
                                )
                            ))
                        ),
                        static fn (Builder $query) => collect($column)->each(static fn ($item) => $query->orWhere(
                            static fn (Builder $qq) => $qq->orWhereJsonContains($key, [$item => $terms])
                        ))
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

        if (is_null($filters)) {
            return $this;
        }

        $filters->each(function (Field $filter): void {
            if ($filter->getRequestValue() === false) {
                return;
            }

            $filterApply = appliesRegister()->findByField($filter, 'filters', ModelResource::class);

            $defaultApply = static fn (Builder $query): Builder => $query->where(
                $filter->getColumn(),
                $filter->getRequestValue()
            );

            if ($filterApply instanceof ApplyContract) {
                $filter->onApply($filterApply->apply($filter));
            } elseif (! $filter->hasOnApply()) {
                $filter->onApply($defaultApply);
            }

            $filter->apply(
                $defaultApply,
                $this->newQuery()
            );
        });

        return $this;
    }

    /**
     * @throws ResourceException
     */
    protected function withParentResource(): static
    {
        $relationName = moonshineRequest()->getParentRelationName();
        $parentId = moonshineRequest()->getParentRelationId();

        if (is_null($relationName) || is_null($parentId)) {
            return $this;
        }

        if (! method_exists($this->getDataInstance(), $relationName)) {
            throw new ResourceException("Relation $relationName not found for current resource");
        }

        $relation = $this->getDataInstance()->{$relationName}();

        $this->newQuery()->when(
            $relation instanceof BelongsToMany,
            static fn (Builder $q) => $q->whereRelation(
                $relationName,
                $relation->getQualifiedRelatedKeyName(),
                $parentId
            ),
            static fn (Builder $q) => $q->where(
                $relation->getForeignKeyName(),
                $parentId
            )
        );

        return $this;
    }

    /**
     * @throws Throwable
     */
    protected function withOrder(): static
    {
        [$column, $direction, $callback] = $this->prepareOrder();

        if ($callback instanceof Closure) {
            $callback($this->newQuery(), $column, $direction);
        } else {
            $this->newQuery()->orderBy($column, $direction);
        }

        return $this;
    }

    /**
     * @return list<QueryTag>
     */
    public function getQueryTags(): array
    {
        return $this->queryTags();
    }

    /**
     * @return string[]
     */
    public function getParentRelations(): array
    {
        return $this->parentRelations;
    }
}
