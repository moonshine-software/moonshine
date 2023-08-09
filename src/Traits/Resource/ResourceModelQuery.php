<?php

declare(strict_types=1);

namespace MoonShine\Traits\Resource;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use MoonShine\Filters\Filter;
use Throwable;

trait ResourceModelQuery
{
    public static array $with = [];

    public static string $orderField = '';

    public static string $orderType = 'DESC';

    public static int $itemsPerPage = 25;

    public static bool $simplePaginate = false;

    protected bool $usePagination = true;

    protected ?Builder $customBuilder = null;

    public static function getItemsPerPage(): int
    {
        return static::$itemsPerPage;
    }

    /**
     * @throws Throwable
     */
    public function paginate(): Paginator
    {
        $paginator = $this->resolveQuery()
            ->when(
                static::$simplePaginate,
                fn (Builder $query): Paginator => $query->simplePaginate(
                    static::getItemsPerPage()
                ),
                fn (Builder $query): LengthAwarePaginator => $query->paginate(
                    static::getItemsPerPage()
                ),
            )
            ->appends(request()->except('page'));

        return $paginator->setCollection(
            $this->transformToResources($paginator->getCollection())
        );
    }

    /**
     * @throws Throwable
     */
    public function resolveQuery(): Builder
    {
        $query = $this->query();

        if ($this->scopes()) {
            foreach ($this->scopes() as $scope) {
                $query = $query->withGlobalScope($scope::class, $scope);
            }
        }

        if (request()->has('search') && count($this->search())) {
            $query = $query->where(function (Builder $q): void {
                foreach ($this->search() as $field) {
                    $q->orWhere(
                        $field,
                        'LIKE',
                        '%' . request('search') . '%'
                    );
                }
            });
        }

        $query = $this->performOrder(
            $query,
            request('order.field', $this->sortColumn()),
            request('order.type', $this->sortDirection())
        );

        if (request()->has('filters') && count($this->filters())) {
            $this->getFilters()
                ->onlyFields()
                ->each(
                    fn (Filter $filter): Builder => $filter->getQuery($query)
                );
        }

        Cache::forget($this->queryCacheKey());
        Cache::remember(
            $this->queryCacheKey(),
            now()->addHours(2),
            static fn () => request()->getQueryString()
        );

        return $query;
    }

    public function query(): Builder
    {
        $query = $this->customBuilder ?? $this->getModel()->query();

        if ($this->hasWith()) {
            $query->with($this->getWith());
        }

        return $query;
    }

    public function performOrder(Builder $query, string $column, string $direction): Builder
    {
        return $query->orderBy($column, $direction);
    }

    public function hasWith(): bool
    {
        return static::$with !== [];
    }

    public function getWith(): array
    {
        return static::$with;
    }

    public function sortColumn(): string
    {
        return static::$orderField ?: $this->getModel()->getKeyName();
    }

    public function sortDirection(): string
    {
        return in_array(strtolower(static::$orderType), ['asc', 'desc'])
            ? static::$orderType
            : 'DESC';
    }

    protected function queryCacheKey(): string
    {
        return "moonshine_query_{$this->routeNameAlias()}";
    }

    public function transformToResources(Collection $collection): Collection
    {
        return $collection->transform(
            fn ($value) => (clone $this)->setItem($value)
        );
    }

    /**
     * @throws Throwable
     */
    public function items(): Collection
    {
        return $this->transformToResources(
            $this->resolveQuery()->get()
        );
    }

    public function customBuilder(Builder $builder): void
    {
        $this->customBuilder = $builder;
    }

    public function isPaginationUsed(): bool
    {
        return $this->usePagination;
    }
}
