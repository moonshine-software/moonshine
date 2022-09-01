<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits\Resource;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

trait ResourceQuery
{
    public static array $with = [];

    public static string $sortColumn = 'id';

    public static string $sortDirection = 'DESC';

    public static int $itemsPerPage = 25;

    public function paginate(): LengthAwarePaginator
    {
        return $this->query()->paginate(static::$itemsPerPage);
    }

    public function query(): Builder
    {
        $query = $this->getModel()->query();

        if ($this->scopes()) {
            foreach ($this->scopes() as $scope) {
                $query->withGlobalScope($scope::class, $scope);
            }
        }

        if (static::$with) {
            $query->with(static::$with);
        }

        if (request()->has('search') && !empty($this->search())) {
            request()->str('search')->explode(' ')->filter()->each(function ($term) use ($query) {
                $query->where(function ($q) use ($term) {
                    foreach ($this->search() as $column) {
                        $q->orWhere($column, 'LIKE', $term.'%');
                    }
                });
            });
        }

        if (request()->has('filters')) {
            foreach ($this->filters() as $filter) {
                $query = $filter->getQuery($query);
            }
        }

        return $query->orderBy(
            request('sort.column', static::$sortColumn),
            request('sort.direction', static::$sortDirection)
        );
    }
}
