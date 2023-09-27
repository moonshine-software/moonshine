<?php

declare(strict_types=1);

namespace MoonShine\Traits\Resource;

use App\Models\Comment;
use App\MoonShine\Resources\CategoryResource;
use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use MoonShine\Attributes\SearchUsingFullText;
use MoonShine\Contracts\ApplyContract;
use MoonShine\Fields\Field;
use MoonShine\MoonShineRouter;
use MoonShine\QueryTags\QueryTag;
use MoonShine\Resources\ModelResource;
use MoonShine\Support\Attributes;
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

    protected array $parentRelations = [];

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
     * @return array<QueryTag>
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
            ->resolveParentResource()
            ->resolveOrder(
                request('sort.column', $this->sortColumn()),
                request('sort.direction', $this->sortDirection())
            )
            ->resolveCachedBackRequest();

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

    protected function resolveCachedBackRequest(): self
    {
        Cache::forget($this->queryCacheKey());

        Cache::remember(
            $this->queryCacheKey(),
            now()->addHours(2),
            static fn () => Arr::query(request()->only(['sort', 'filters', 'query-tag']))
        );

        return $this;
    }

    protected function resolveTags(): self
    {
        if ($tagUri = request('query-tag')) {
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

    protected function resolveSearch(): self
    {
        if (! empty($this->search()) && request()->has('search')) {
            $fullTextColumns = Attributes::for($this)
                ->method('search')
                ->attribute(SearchUsingFullText::class)
                ->attributeProperty('columns')
                ->get();

            $terms = request()
                ->str('search')
                ->squish()
                ->value();

            if (! is_null($fullTextColumns)) {
                $this->query()->whereFullText($fullTextColumns, $terms);
            } else {
                $this->searchQuery($terms);
            }
        }

        return $this;
    }

    protected function searchQuery(string $terms): void
    {
        $this->query()->where(function (Builder $builder) use ($terms): void {
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
                                fn (Builder $qq) => $qq->orWhere($item, 'LIKE', "%$terms%")
                            ))
                        ),
                        fn (Builder $query) => collect($column)->each(fn ($item) => $query->where(
                            fn (Builder $qq) => $qq->orWhereJsonContains("$key->$item", $terms)
                        ))
                    );
                } else {
                    $builder->orWhere($column, 'LIKE', "%$terms%");
                }
            }
        });
    }

    protected function resolveOrder(string $column, string $direction): self
    {
        $this->query()->orderBy($column, $direction);

        return $this;
    }

    protected function resolveFilters(): self
    {
        $filters = $this->getFilters()->onlyFields();

        $filters->fill(
            request('filters', $this->getModel()->toArray()),
            $this->getModel()
        );

        $filters->each(function (Field $filter): void {
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

        return $this;
    }

    protected function resolveParentResource(): self
    {
        if(
            is_null($relation = moonshineRequest()->getParentRelationName())
            || is_null($parentId = moonshineRequest()->getParentRelationId())
        ) {
            return $this;
        }

        if(! empty($this->parentRelations())) {
            foreach ($this->parentRelations() as $relationName) {
                if($relation == $relationName) {
                    $this->query()->where(
                        $this->getModel()->{$relation}()->getForeignKeyName(),
                        $parentId
                    );

                    return $this;
                }
            }
        }

        if(
            method_exists($this->getModel(), $relation)
            && method_exists($this->getModel()->{$relation}(), 'getForeignKeyName')
        ) {
            $this->query()->where(
                $this->getModel()->{$relation}()->getForeignKeyName(),
                $parentId
            );

            return $this;
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

    public function parentRelations(): array
    {
        return $this->parentRelations;
    }
}
