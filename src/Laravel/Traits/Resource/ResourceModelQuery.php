<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Resource;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use MoonShine\Contracts\UI\ApplyContract;
use MoonShine\Core\Exceptions\ResourceException;
use MoonShine\Laravel\QueryTags\QueryTag;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Laravel\Support\DBOperators;
use MoonShine\Support\Attributes;
use MoonShine\Support\Attributes\SearchUsingFullText;
use MoonShine\UI\Fields\Field;
use Throwable;

/**
 * @template-covariant TModel of Model
 */
trait ResourceModelQuery
{
    /** @var ?TModel */
    protected ?Model $item = null;

    protected array $with = [];

    protected string $sortColumn = '';

    protected string $sortDirection = 'DESC';

    protected int $itemsPerPage = 25;

    protected bool $usePagination = true;

    protected bool $simplePaginate = false;

    protected ?Builder $query = null;

    protected ?Builder $customBuilder = null;

    protected int|string|false|null $itemID = null;

    protected array $parentRelations = [];

    protected bool $saveQueryState = false;

    protected ?int $paginatorPage = null;

    protected iterable $queryParams = [];

    public function setQueryParams(iterable $params): static
    {
        $this->queryParams = $params;

        return $this;
    }

    public function getQueryParams(): Collection
    {
        return collect($this->queryParams);
    }

    public function setItemID(int|string|false|null $itemID): static
    {
        $this->itemID = $itemID;

        return $this;
    }

    public function getItemID(): int|string|null
    {
        if ($this->itemID === false) {
            return null;
        }

        return $this->itemID ?? moonshineRequest()->getItemID();
    }

    /**
     * @return ?TModel
     */
    protected function itemOr(Closure $closure): ?Model
    {
        if (! is_null($this->item)) {
            return $this->item;
        }

        $this->item = $closure();

        return $this->item;
    }

    protected function resolveItemQuery(): Builder
    {
        return $this->getModel()->newQuery();
    }

    /**
     * @return ?TModel
     */
    public function getItem(): ?Model
    {
        if (! is_null($this->item)) {
            return $this->item;
        }

        if (is_null($this->getItemID())) {
            return null;
        }

        return $this->itemOr(
            fn () => $this
                ->resolveItemQuery()
                ->find($this->getItemID())
        );
    }

    /**
     * @param  ?TModel $model
     */
    public function setItem(?Model $model): static
    {
        $this->item = $model;

        return $this;
    }

    /**
     * @return TModel
     */
    public function getItemOrInstance(): Model
    {
        if (! is_null($this->item)) {
            return $this->item;
        }

        if (is_null($this->getItemID())) {
            return $this->getModel();
        }

        return $this->itemOr(
            fn () => $this
                ->resolveItemQuery()
                ->findOrNew($this->getItemID())
        );
    }

    /**
     * @return TModel
     */
    public function getItemOrFail(): Model
    {
        if (! is_null($this->item)) {
            return $this->item;
        }

        return $this->itemOr(
            fn () => $this
                ->resolveItemQuery()
                ->findOrFail($this->getItemID())
        );
    }

    /**
     * Get an array of custom form actions
     *
     * @return list<QueryTag>
     */
    public function queryTags(): array
    {
        return [];
    }

    protected function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }

    public function setPaginatorPage(?int $page): static
    {
        $this->paginatorPage = $page;

        return $this;
    }

    public function getPaginatorPage(): int
    {
        $page = $this->paginatorPage ?? (int) $this->getQueryParams()->get('page');

        if ($this->isSaveQueryState() && ! $this->getQueryParams()->has('reset')) {
            return (int) data_get(
                moonshineCache()->get($this->getQueryCacheKey(), []),
                'page',
                $page
            );
        }

        return $page;
    }

    /**
     * @throws Throwable
     */
    public function paginate(): Paginator
    {
        return $this->resolveQuery()
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

    public function isSimplePaginate(): bool
    {
        return $this->simplePaginate;
    }

    /**
     * @throws Throwable
     */
    public function resolveQuery(): Builder
    {
        $this
            ->resolveCache()
            ->resolveTags()
            ->resolveSearch()
            ->resolveFilters()
            ->resolveParentResource()
            ->resolveOrder()
            ->cacheQueryParams();

        return $this->getQuery();
    }

    public function query(): Builder
    {
        if (! is_null($this->query)) {
            return $this->query;
        }

        $this->query = $this->customBuilder ?? $this->getModel()->newQuery();

        if ($this->hasWith()) {
            $this->query->with($this->getWith());
        }

        return $this->query;
    }

    public function getQuery(): Builder
    {
        return $this->query ?: $this->query();
    }

    public function isSaveQueryState(): bool
    {
        return $this->saveQueryState;
    }

    /**
     * @return string[]
     */
    public function getQueryParamsKeys(): array
    {
        return ['sort', 'filters', 'page', 'query-tag', 'search'];
    }

    /**
     * @return string[]
     */
    protected function getCachedRequestKeys(): array
    {
        return $this->getQueryParamsKeys();
    }

    protected function cacheQueryParams(): static
    {
        if (! $this->isSaveQueryState()) {
            return $this;
        }

        if ($this->getQueryParams()->has('reset')) {
            moonshineCache()->forget($this->getQueryCacheKey());

            return $this;
        }

        if ($this->getQueryParams()->hasAny($this->getCachedRequestKeys())) {
            moonshineCache()->put(
                $this->getQueryCacheKey(),
                $this->getQueryParams()->only($this->getCachedRequestKeys()),
                now()->addHours(2)
            );
        }

        return $this;
    }

    protected function resolveCache(): static
    {
        if ($this->isSaveQueryState()
            && ! $this->getQueryParams()->hasAny([
                ...$this->getCachedRequestKeys(),
                'reset',
            ])
        ) {
            $this->setQueryParams(
                $this->getQueryParams()->merge(
                    collect(moonshineCache()->get($this->getQueryCacheKey(), []))->filter(
                        fn ($value, $key): bool => ! $this->getQueryParams()->has($key)
                    )->toArray()
                )
            );
        }

        return $this;
    }

    protected function resolveTags(): static
    {
        /** @var QueryTag $tag */
        $tag = collect($this->queryTags())
            ->first(
                static fn (QueryTag $tag): bool => $tag->isActive()
            );

        if ($tag) {
            $this->customBuilder(
                $tag->apply(
                    $this->getQuery()
                )
            );
        }

        return $this;
    }

    protected function resolveSearch($queryKey = 'search'): static
    {
        if (! empty($this->search()) && filled($this->getQueryParams()->get($queryKey))) {
            $fullTextColumns = Attributes::for($this)
                ->attribute(SearchUsingFullText::class)
                ->method('search')
                ->attributeProperty('columns')
                ->get();

            $terms = str($this->getQueryParams()->get($queryKey))
                ->squish()
                ->value();

            if (! is_null($fullTextColumns)) {
                $this->getQuery()->whereFullText($fullTextColumns, $terms);
            } else {
                $this->searchQuery($terms);
            }
        }

        return $this;
    }

    protected function searchQuery(string $terms): void
    {
        $this->getQuery()->where(function (Builder $builder) use ($terms): void {
            foreach ($this->search() as $key => $column) {
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
                        method_exists($this->getModel(), $key),
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
    protected function resolveOrder(): static
    {
        $column = $this->getSortColumn();
        $direction = $this->getSortDirection();

        if (($sort = $this->getQueryParams()->get('sort')) && is_string($sort)) {
            $column = ltrim($sort, '-');
            $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
        }

        $field = $this->getIndexFields()->findByColumn($column);

        $callback = $field?->getSortableCallback();

        if (is_string($callback)) {
            $column = value($callback);
        }

        if ($callback instanceof Closure) {
            $callback($this->getQuery(), $column, $direction);
        } else {
            $this->getQuery()
                ->orderBy($column, $direction);
        }

        return $this;
    }

    /**
     * @return array<array-key, mixed>
     */
    public function getFilterParams(): array
    {
        $default = $this->getQueryParams()->get('filters', []);

        if ($this->isSaveQueryState()) {
            return data_get(
                moonshineCache()->get($this->getQueryCacheKey(), []),
                'filters',
                $default
            );
        }

        return $default;
    }

    /**
     * @throws Throwable
     */
    protected function resolveFilters(): static
    {
        $params = $this->getFilterParams();

        if (blank($params)) {
            return $this;
        }

        $filters = $this->getFilters()->onlyFields();

        $filters->fill(
            $params,
            $this->getModelCast()->cast($this->getModel())
        );

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
                $this->getQuery()
            );
        });

        return $this;
    }

    /**
     * @throws ResourceException
     */
    protected function resolveParentResource(): static
    {
        $relationName = moonshineRequest()->getParentRelationName();
        $parentId = moonshineRequest()->getParentRelationId();

        if (is_null($relationName) || is_null($parentId)) {
            return $this;
        }

        if (! method_exists($this->getModel(), $relationName)) {
            throw new ResourceException("Relation $relationName not found for current resource");
        }

        $relation = $this->getModel()->{$relationName}();

        $this->getQuery()->when(
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

    public function getSortDirection(): string
    {
        return in_array(strtolower($this->sortDirection), ['asc', 'desc'])
            ? $this->sortDirection
            : 'DESC';
    }

    protected function getQueryCacheKey(): string
    {
        return "moonshine_query_{$this->getUriKey()}";
    }

    /**
     * @throws Throwable
     */
    public function getItems(): Collection
    {
        return $this->resolveQuery()->get();
    }

    public function customBuilder(Builder $builder): static
    {
        $this->customBuilder = $builder;

        return $this;
    }

    public function isPaginationUsed(): bool
    {
        return $this->usePagination;
    }

    /**
     * @return string[]
     */
    public function getParentRelations(): array
    {
        return $this->parentRelations;
    }
}
