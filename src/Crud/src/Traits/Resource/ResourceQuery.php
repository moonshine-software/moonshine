<?php

declare(strict_types=1);

namespace MoonShine\Crud\Traits\Resource;

use Attribute;
use Closure;
use DateInterval;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;
use Leeto\FastAttributes\Attributes;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Crud\Collections\Fields;
use MoonShine\Support\Attributes\SearchUsingFullText;
use MoonShine\Support\Enums\SortDirection;
use MoonShine\UI\Contracts\RangeFieldContract;
use Throwable;

/**
 * @template T of mixed = mixed
 * @template TFields of Fields = Fields
 */
trait ResourceQuery
{
    /** @var null|T */
    protected mixed $item = null;

    protected string $sortColumn = '';

    protected string $searchQueryKey = 'search';

    protected SortDirection $sortDirection = SortDirection::DESC;

    protected int $itemsPerPage = 25;

    protected bool $usePagination = true;

    protected bool $simplePaginate = false;

    protected bool $cursorPaginate = false;

    protected int|string|false|null $itemID = null;

    protected bool $stopGettingItemFromUrl = false;

    protected bool $saveQueryState = false;

    protected ?int $paginatorPage = null;

    /**
     * @var iterable<array-key, mixed>
     */
    protected iterable $queryParams = [];

    protected string $queryParamPrefix = '';

    /**
     * @return iterable<T>|Collection<array-key, T>|LazyCollection<array-key, T>|CursorPaginator<array-key, T>|Paginator<array-key, T>
     */
    abstract public function getItems(): iterable|Collection|LazyCollection|CursorPaginator|Paginator;

    /**
     * @return null|DataWrapperContract<T>
     */
    abstract public function findItem(bool $orFail = false): ?DataWrapperContract;

    public function setItemID(int|string|false|null $itemID): static
    {
        $this->itemID = $itemID;

        return $this;
    }

    public function stopGettingItemFromUrl(): static
    {
        $this->stopGettingItemFromUrl = true;

        return $this->setItem(null);
    }

    public function isStopGettingItemFromUrl(): bool
    {
        return $this->stopGettingItemFromUrl;
    }

    public function getItemID(): int|string|null
    {
        // false is the value that stops the logic
        if ($this->itemID === false) {
            return null;
        }

        if (! blank($this->itemID)) {
            return $this->itemID;
        }

        if ($this->isStopGettingItemFromUrl()) {
            return null;
        }

        return $this->getCore()->getCrudRequest()->getItemID();
    }

    /**
     * @return null|T
     */
    protected function itemOr(Closure $callback): mixed
    {
        if (! \is_null($this->item)) {
            return $this->item;
        }

        $this->item = $callback();

        return $this->item;
    }

    /**
     * @param  null|T  $item
     */
    public function setItem(mixed $item): static
    {
        $this->item = $item;

        return $this;
    }

    public function isItemExists(): bool
    {
        return ! \is_null($this->getCastedData()?->getKey());
    }

    /**
     * @return null|T
     */
    public function getItem(): mixed
    {
        if (! \is_null($this->item)) {
            return $this->item;
        }

        if (\is_null($this->getItemID())) {
            return null;
        }

        return $this->itemOr(
            fn () => $this->findItem()?->getOriginal(),
        );
    }

    /**
     * @return T
     */
    public function getItemOrInstance(): mixed
    {
        if (! \is_null($this->item)) {
            return $this->item;
        }

        if (\is_null($this->getItemID())) {
            return $this->getDataInstance();
        }

        return $this->itemOr(
            fn () => $this->findItem()?->getOriginal() ?? $this->getDataInstance(),
        );
    }

    /**
     * @return T
     * @throws Throwable
     */
    public function getItemOrFail(): mixed
    {
        if (! \is_null($this->item)) {
            return $this->item;
        }

        return $this->itemOr(
            fn () => $this->findItem(orFail: true)->getOriginal(),
        );
    }

    protected function withSearch(string $queryKey): static
    {
        $term = data_get($this->getQueryParams(), $queryKey);

        if ($this->hasSearch() && filled($term)) {
            $fullTextColumns = $this->getCore()->getAttributes()->get(
                default: fn (): mixed => Attributes::for($this)
                    ->attribute(SearchUsingFullText::class)
                    ->method('search')
                    ->first('columns'),
                target: $this::class,
                attribute: SearchUsingFullText::class,
                type: Attribute::TARGET_METHOD,
                column: [0 => 'columns']
            );

            $terms = Str::of($term)
                ->squish()
                ->value();

            return $this->resolveSearch($terms, $fullTextColumns);
        }

        return $this;
    }

    /**
     * @param  iterable<string, string>|null  $fullTextColumns
     */
    protected function resolveSearch(string $terms, ?iterable $fullTextColumns = null): static
    {
        //

        return $this;
    }

    public function getSortColumn(): string
    {
        return $this->sortColumn;
    }

    public function getSortDirection(): string
    {
        return $this->sortDirection->value;
    }

    /**
     * @return array<array-key, mixed>
     * @throws Throwable
     */
    protected function prepareOrder(): array
    {
        $column = $this->getSortColumn();
        $direction = $this->getSortDirection();

        if (($sort = $this->getQueryParam('sort')) && \is_string($sort)) {
            $column = ltrim($sort, '-');
            $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
        }

        $field = $this->getIndexFields()->findByColumn($column);

        $callback = $field?->getSortableCallback();

        if (\is_string($callback)) {
            $column = $callback;
            $callback = null;
        }

        return [$column, $direction, $callback];
    }

    /**
     * to specify data from a request in console mode
     *
     * @param iterable<string, mixed> $params
     */
    public function setQueryParams(iterable $params): static
    {
        $this->queryParams = $params;

        return $this;
    }

    /**
     * @return Collection<string, mixed>
     */
    public function getQueryParams(): Collection
    {
        return new Collection($this->queryParams);
    }

    public function getQueryParamPrefix(): string
    {
        return $this->queryParamPrefix;
    }

    public function getQueryParamName(string $key): string
    {
        return $this->queryParamPrefix . $key;
    }

    public function getQueryParam(string $key, mixed $default = null): mixed
    {
        return $this->getQueryParams()->get($this->getQueryParamName($key), $default);
    }

    public function hasQueryParam(string $key): bool
    {
        return $this->getCore()->getRequest()->has(
            $this->getQueryParamName($key)
        );
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

    protected function getPaginatorPage(): int
    {
        $page = $this->paginatorPage ?? (int)$this->getQueryParam('page');

        if ($this->isSaveQueryState() && ! $this->hasQueryParam('reset')) {
            return (int)data_get(
                $this->getCore()->getCache()->get($this->getQueryCacheKey(), []),
                'page',
                $page,
            );
        }

        return $page;
    }

    protected function isSimplePaginate(): bool
    {
        return $this->simplePaginate;
    }

    protected function isCursorPaginate(): bool
    {
        return $this->cursorPaginate;
    }

    public function isPaginationUsed(): bool
    {
        return $this->usePagination;
    }

    protected function isSaveQueryState(): bool
    {
        return $this->saveQueryState;
    }

    public function disableSaveQueryState(): static
    {
        $this->saveQueryState = false;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getQueryParamsKeys(): array
    {
        return [
            $this->getQueryParamName('sort'),
            $this->getQueryParamName('filter'),
            $this->getQueryParamName('page'),
            $this->getQueryParamName('query-tag'),
            $this->getSearchQueryKey(),
        ];
    }

    /**
     * @return string
     */
    protected function getSearchQueryKey(): string
    {
        return $this->searchQueryKey;
    }

    /**
     * @return string[]
     */
    protected function getCachedRequestKeys(): array
    {
        return $this->getQueryParamsKeys();
    }

    protected function withCachedQueryParams(): static
    {
        if (! $this->isSaveQueryState()) {
            return $this;
        }

        if ($this->hasQueryParam('reset')) {
            $this->getCore()->getCache()->delete($this->getQueryCacheKey());

            return $this;
        }

        if ($this->getQueryParams()->hasAny($this->getCachedRequestKeys())) {
            $this->getCore()->getCache()->set(
                $this->getQueryCacheKey(),
                $this->getQueryParams()->only($this->getCachedRequestKeys()),
                new DateInterval('PT2H'),
            );
        }

        return $this;
    }

    protected function getQueryCacheKey(): string
    {
        return "moonshine_query_{$this->getUriKey()}";
    }

    protected function withCache(): static
    {
        if ($this->isSaveQueryState()
            && ! $this->hasQueryParam('reset')
            && ! $this->getQueryParams()->hasAny($this->getCachedRequestKeys())
        ) {
            /** @var Collection<string, mixed> $collection */
            $collection = new Collection($this->getCore()->getCache()->get($this->getQueryCacheKey(), []));

            $this->setQueryParams(
                $this->getQueryParams()->merge(
                    $collection->filter(
                        fn (mixed $value, string $key): bool => ! $this->hasQueryParam($key),
                    )->toArray(),
                ),
            );
        }

        return $this;
    }

    /**
     * @return TFields|null
     * @throws Throwable
     */
    protected function prepareFilters(): ?FieldsContract
    {
        $params = $this->getFilterParams();

        if (blank($params)) {
            return null;
        }

        $filters = $this->getFilters()->onlyFields();

        foreach ($filters as $filter) {
            if ($filter instanceof RangeFieldContract) {
                data_forget($params, $filter->getColumn());
            }
        }

        $filters->fill(
            $params,
            $this->getCaster()->cast($params),
        );

        return $filters;
    }
}
