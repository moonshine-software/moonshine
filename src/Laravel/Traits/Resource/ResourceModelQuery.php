<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Resource;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use MoonShine\Core\Exceptions\ResourceException;
use MoonShine\Laravel\QueryTags\QueryTag;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Laravel\Support\DBOperators;
use MoonShine\Support\Attributes;
use MoonShine\Support\Attributes\SearchUsingFullText;
use MoonShine\UI\Contracts\ApplyContract;
use MoonShine\UI\Fields\Field;
use Throwable;

/**
 * @template-covariant TModel of Model
 */
trait ResourceModelQuery
{
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
        if($this->itemID === false) {
            return null;
        }

        return $this->itemID ?? moonshineRequest()->getItemID();
    }

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
        return $this->getModel()
            ->newQuery();
    }

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
     * @return $this
     */
    public function setItem(?Model $model): static
    {
        $this->item = $model;

        return $this;
    }

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
     * @return QueryTag
     */
    public function queryTags(): array
    {
        return [];
    }

    protected function itemsPerPage(): int
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

        if ($this->saveQueryState() && ! $this->getQueryParams()->has('reset')) {
            return (int) data_get(
                moonshineCache()->get($this->queryCacheKey(), []),
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
                    $this->itemsPerPage(),
                    page: $this->getPaginatorPage()
                ),
                fn (Builder $query): LengthAwarePaginator => $query->paginate(
                    $this->itemsPerPage(),
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

        $this->query = $this->customBuilder ?? $this->getModel()->query();

        if ($this->hasWith()) {
            $this->query->with($this->getWith());
        }

        return $this->query;
    }

    public function getQuery(): Builder
    {
        return $this->query ?: $this->query();
    }

    public function saveQueryState(): bool
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
    protected function cachedRequestKeys(): array
    {
        return $this->getQueryParamsKeys();
    }

    protected function cacheQueryParams(): static
    {
        if (! $this->saveQueryState()) {
            return $this;
        }

        if ($this->getQueryParams()->has('reset')) {
            moonshineCache()->forget($this->queryCacheKey());

            return $this;
        }

        if ($this->getQueryParams()->hasAny($this->cachedRequestKeys())) {
            moonshineCache()->put(
                $this->queryCacheKey(),
                $this->getQueryParams()->only($this->cachedRequestKeys()),
                now()->addHours(2)
            );
        }

        return $this;
    }

    protected function resolveCache(): static
    {
        if ($this->saveQueryState()
            && ! $this->getQueryParams()->hasAny([
                ...$this->cachedRequestKeys(),
                'reset',
            ])
        ) {
            $this->setQueryParams(
                $this->getQueryParams()->merge(
                    collect(moonshineCache()->get($this->queryCacheKey(), []))->filter(fn ($value, $key): bool => ! $this->getQueryParams()->has($key))->toArray()
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
                fn (QueryTag $tag): bool => $tag->isActive()
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
                        ->tap(function (Collection $data) use (&$key): void {
                            $key = $data->first();
                        })
                        ->slice(-1)
                        ->values()
                        ->toArray();
                }

                if (is_array($column)) {
                    $builder->when(
                        method_exists($this->getModel(), $key),
                        fn (Builder $query) => $query->orWhereHas(
                            $key,
                            fn (Builder $q) => collect($column)->each(fn ($item) => $q->where(
                                fn (Builder $qq) => $qq->orWhere(
                                    $item,
                                    DBOperators::byModel($qq->getModel())->like(),
                                    "%$terms%"
                                )
                            ))
                        ),
                        fn (Builder $query) => collect($column)->each(fn ($item) => $query->orWhere(
                            fn (Builder $qq) => $qq->orWhereJsonContains($key, [$item => $terms])
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
        $column = $this->sortColumn();
        $direction = $this->sortDirection();

        if (($sort = $this->getQueryParams()->get('sort')) && is_string($sort)) {
            $column = ltrim($sort, '-');
            $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
        }

        $field = $this->getIndexFields()->findByColumn($column);

        $callback = $field?->sortableCallback();

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

        if ($this->saveQueryState()) {
            return data_get(
                moonshineCache()->get($this->queryCacheKey(), []),
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
        if (
            is_null($relation = moonshineRequest()->getParentRelationName())
            || is_null($parentId = moonshineRequest()->getParentRelationId())
        ) {
            return $this;
        }

        if (! empty($this->parentRelations())) {
            foreach ($this->parentRelations() as $relationName) {
                if ($relation === $relationName) {
                    $this->getQuery()->where(
                        $this->getModel()->{$relation}()->getForeignKeyName(),
                        $parentId
                    );

                    return $this;
                }
            }
        }

        if (
            method_exists($this->getModel(), $relation)
            && method_exists($this->getModel()->{$relation}(), 'getForeignKeyName')
        ) {
            $this->getQuery()->where(
                $this->getModel()->{$relation}()->getForeignKeyName(),
                $parentId
            );

            return $this;
        }

        throw new ResourceException("Relation $relation not found for current resource");
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

    public function sortColumn(): string
    {
        return $this->sortColumn ?: $this->getModel()->getKeyName();
    }

    public function sortDirection(): string
    {
        return in_array(strtolower($this->sortDirection), ['asc', 'desc'])
            ? $this->sortDirection
            : 'DESC';
    }

    protected function queryCacheKey(): string
    {
        return "moonshine_query_{$this->getUriKey()}";
    }

    /**
     * @throws Throwable
     */
    public function items(): Collection
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
    public function parentRelations(): array
    {
        return $this->parentRelations;
    }
}
