<?php

declare(strict_types=1);

namespace MoonShine\Traits\Resource;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use MoonShine\Actions\FiltersAction;
use MoonShine\Fields\Field;
use Throwable;

trait ResourceModelQuery
{
    protected ?Model $item = null;

    protected array $with = [];

    protected string $sortColumn = '';

    protected string $sortDirection = 'DESC';

    protected int $itemsPerPage = 25;

    protected bool $usePagination = true;

    protected bool $simplePaginate = false;

    protected ?Builder $customBuilder = null;

    public function getItemID(): int|string|null
    {
        return request('resourceItem');
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
        return $this->getModel()->newQuery();
    }

    public function getItem(): ?Model
    {
        return $this->itemOr(
            fn () => $this
                ->resolveItemQuery()
                ->find($this->getItemID())
        );
    }

    public function getItemOrInstance(): Model
    {
        return $this->itemOr(
            fn () => $this
                ->resolveItemQuery()
                ->findOrNew($this->getItemID())
        );
    }

    public function getItemOrFail(): Model
    {
        return $this->itemOr(
            fn () => $this
                ->resolveItemQuery()
                ->findOrFail($this->getItemID())
        );
    }

    /**
     * @throws Throwable
     */
    public function paginate(): Paginator
    {
        return $this->resolveQuery()
            ->when(
                $this->simplePaginate,
                fn (Builder $query): Paginator => $query->simplePaginate(
                    $this->itemsPerPage
                ),
                fn (Builder $query): LengthAwarePaginator => $query->paginate(
                    $this->itemsPerPage
                ),
            )
            ->appends(request()->except('page'));
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

        if (! empty($this->search()) && request()->has('search')) {
            request()->str('search')->explode(' ')->filter()->each(function ($term) use ($query): void {
                $query->where(function ($q) use ($term): void {
                    foreach ($this->search() as $column) {
                        $q->orWhere($column, 'LIKE', $term . '%');
                    }
                });
            });
        }

        $query = $this->resolveOrder(
            $query,
            request('sort.column', $this->sortColumn()),
            request('sort.direction', $this->sortDirection())
        );

        if (request()->has('filters') && count($this->filters())) {
            $this->getFilters()
                ->each(function (Field $filter) use ($query) {
                    if(empty($filter->requestValue())) {
                        return;
                    }

                    if($filterApply = modelApplyFilter($filter)) {
                        $filter->onApply($filterApply->apply($filter));
                    }

                    $filter->apply($this->filterApply($filter), $query);
                });
        }

        Cache::forget($this->queryCacheKey());
        Cache::remember(
            $this->queryCacheKey(),
            now()->addHours(2),
            static fn () => request()->getQueryString()
        );

        return $query;
    }

    protected function query(): Builder
    {
        $query = $this->customBuilder ?? $this->getModel()->query();

        if ($this->hasWith()) {
            $query->with($this->getWith());
        }

        return $query;
    }

    public function scopes(): array
    {
        return [];
    }

    protected function resolveOrder(Builder $query, string $column, string $direction): Builder
    {
        return $query->orderBy($column, $direction);
    }

    public function hasWith(): bool
    {
        return $this->with !== [];
    }

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
        return "moonshine_query_{$this->uriKey()}";
    }

    /**
     * @throws Throwable
     */
    public function items(): Collection
    {
        return $this->resolveQuery()->get();
    }

    public function customBuilder(Builder $builder): void
    {
        $this->customBuilder = $builder;
    }

    public function isPaginationUsed(): bool
    {
        return $this->usePagination;
    }

    public function filterApply(Field $filter): Closure
    {
        return fn(Builder $query) => $query->where($filter->column(), $filter->requestValue());
    }
}
