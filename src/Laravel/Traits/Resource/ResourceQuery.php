<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Resource;

use Closure;
use Illuminate\Support\Collection;
use Leeto\FastAttributes\Attributes;
use MoonShine\Laravel\Collections\Fields;
use MoonShine\Support\Attributes\SearchUsingFullText;

/**
 * @template-covariant T
 * @template-covariant C of iterable
 */
trait ResourceQuery
{
    /** @var ?T */
    protected mixed $item = null;

    protected string $sortColumn = '';

    protected string $sortDirection = 'DESC';

    protected int $itemsPerPage = 25;

    protected bool $usePagination = true;

    protected bool $simplePaginate = false;

    protected bool $cursorPaginate = false;

    protected int|string|false|null $itemID = null;

    protected bool $saveQueryState = false;

    protected ?int $paginatorPage = null;

    protected iterable $queryParams = [];

    /**
     * @return C
     */
    abstract public function getItems(): mixed;

    /**
     * @return T
     */
    abstract public function findItem(bool $orFail = false): mixed;

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
     * @return ?T
     */
    protected function itemOr(Closure $closure): mixed
    {
        if (! is_null($this->item)) {
            return $this->item;
        }

        $this->item = $closure();

        return $this->item;
    }

    /**
     * @param  ?T $item
     */
    public function setItem(mixed $item): static
    {
        $this->item = $item;

        return $this;
    }

    public function isItemExists(): bool
    {
        return ! is_null($this->getCastedData()?->getKey());
    }

    /**
     * @return ?T
     */
    public function getItem(): mixed
    {
        if (is_null($this->getItemID())) {
            return null;
        }

        return $this->itemOr(
            fn () => $this->findItem()
        );
    }

    /**
     * @return T
     */
    public function getItemOrInstance(): mixed
    {
        if (is_null($this->getItemID())) {
            return $this->getDataInstance();
        }

        return $this->itemOr(
            fn () => $this->findItem() ?? $this->getDataInstance()
        );
    }

    /**
     * @return T
     */
    public function getItemOrFail(): mixed
    {
        return $this->itemOr(
            fn () => $this->findItem(orFail: true)
        );
    }

    protected function withSearch($queryKey = 'search'): static
    {
        if ($this->hasSearch() && filled($this->getQueryParams()->get($queryKey))) {
            $fullTextColumns = Attributes::for($this)
                ->attribute(SearchUsingFullText::class)
                ->method('search')
                ->first('columns');

            $terms = str($this->getQueryParams()->get($queryKey))
                ->squish()
                ->value();

            return $this->resolveSearch($terms, $fullTextColumns);
        }

        return $this;
    }

    protected function resolveSearch(string $terms, array $fullTextColumns = []): static
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
        return in_array(strtolower($this->sortDirection), ['asc', 'desc'])
            ? $this->sortDirection
            : 'DESC';
    }

    protected function prepareOrder(): array
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

        return [$column, $direction, $callback];
    }

    /**
     * to specify data from a request in console mode
     */
    public function setQueryParams(iterable $params): static
    {
        $this->queryParams = $params;

        return $this;
    }

    public function getQueryParams(): Collection
    {
        return collect($this->queryParams);
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

    /**
     * @return string[]
     */
    public function getQueryParamsKeys(): array
    {
        return ['sort', 'filter', 'page', 'query-tag', 'search'];
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

    protected function getQueryCacheKey(): string
    {
        return "moonshine_query_{$this->getUriKey()}";
    }

    protected function withCache(): static
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

    /**
     * @return array<array-key, mixed>
     */
    public function getFilterParams(): array
    {
        $default = $this->getQueryParams()->get('filter', []);

        if ($this->isSaveQueryState()) {
            return data_get(
                moonshineCache()->get($this->getQueryCacheKey(), []),
                'filter',
                $default
            );
        }

        return $default;
    }

    protected function prepareFilters(): ?Fields
    {
        $params = $this->getFilterParams();

        if (blank($params)) {
            return null;
        }

        $filters = $this->getFilters()->onlyFields();

        $filters->fill(
            $params,
            $this->getCaster()->cast($this->getDataInstance())
        );

        return $filters;
    }
}
