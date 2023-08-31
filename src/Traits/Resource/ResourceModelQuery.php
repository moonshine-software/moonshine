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
use MoonShine\Attributes\SearchUsingFullText;
use MoonShine\Contracts\ApplyContract;
use MoonShine\Fields\Field;
use MoonShine\QueryTags\QueryTag;
use MoonShine\Resources\ModelResource;
use MoonShine\Utilities\Attributes;
use Reflector;
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

    protected ?Builder $query = null;

    protected ?Builder $customBuilder = null;

    public function getItemID(): int|string|null
    {
        return request(
            'resourceItem',
            request()->route('resourceItem')
        );
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
     * Get an array of custom form actions
     *
     * @return array<int, QueryTag>
     */
    public function queryTags(): array
    {
        return [];
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
        $this->resolveTags()
            ->resolveSearch()
            ->resolveFilters()
            ->resolveOrder(
                request('sort.column', $this->sortColumn()),
                request('sort.direction', $this->sortDirection())
            );

        Cache::forget($this->queryCacheKey());
        Cache::remember(
            $this->queryCacheKey(),
            now()->addHours(2),
            static fn () => request()->getQueryString()
        );

        return $this->query();
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

    protected function resolveTags(): self
    {
        if ($tagUri = request('queryTag')) {
            $tag = collect($this->queryTags())
                ->first(
                    fn (QueryTag $tag): bool => $tag->uri() === $tagUri
                );

            if ($tag) {
                $this->customBuilder(
                    $tag->apply(
                        $this->query()
                    )
                );
            }
        }

        return $this;
    }

    /**
     * @return mixed[]
     */
    public function getAttributes(Reflector $reflection): array
    {
        $attributes = $reflection->getAttributes();
        $result = [];
        foreach ($attributes as $attribute) {
            $result[$attribute->getName()] = $attribute->getArguments();
        }

        return $result;
    }

    protected function resolveSearch(): self
    {
        if (! empty($this->search()) && request()->has('search')) {
            $fullTextColumns = Attributes::for($this)
                ->method('search')
                ->attribute(SearchUsingFullText::class)
                ->attributeProperty('columns')
                ->get();

            if (! is_null($fullTextColumns)) {
                $this->query()->whereFullText($fullTextColumns, request()->str('search')->squish());
            } else {
                request()->str('search')->explode(' ')->filter()->each(function ($term): void {
                    $this->query()->where(function (Builder $q) use ($term): void {
                        foreach ($this->search() as $column) {
                            $q->orWhere($column, 'LIKE', $term . '%');
                        }
                    });
                });
            }
        }

        return $this;
    }

    protected function resolveOrder(string $column, string $direction): self
    {
        $this->query()->orderBy($column, $direction);

        return $this;
    }

    protected function resolveFilters(): self
    {
        if (request()->has('filters') && count($this->filters())) {
            $this->getFilters()
                ->onlyFields()
                ->each(function (Field $filter): void {
                    if (empty($filter->requestValue())) {
                        return;
                    }

                    $filterApply = findFieldApply(
                        $filter,
                        'filters',
                        ModelResource::class
                    );

                    if ($filterApply instanceof ApplyContract) {
                        $filter->onApply($filterApply->apply($filter));
                    }

                    $filter->apply(
                        static fn (Builder $query): Builder => $query->where(
                            $filter->column(),
                            $filter->requestValue()
                        ),
                        $this->query()
                    );
                });
        }

        return $this;
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
}
