<?php

namespace Leeto\MoonShine\Traits\Resources;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

trait QueryTrait
{
    public static string $orderField = 'id';

    public static string $orderType = 'DESC';

    public static int $itemsPerPage = 25;

    public function all(): Collection
    {
        return $this->query()->get();
    }

    public function paginate(): LengthAwarePaginator
    {
        return $this->query()->paginate(static::$itemsPerPage);
    }

    public function query(): Builder
    {
        $query = $this->getModel()->query();

        if(static::$with) {
            $query = $query->with(static::$with);
        }

        if(request()->has('search') && count($this->search())) {
            foreach($this->search() as $field) {
                $query = $query->orWhere(
                    $field,
                    'LIKE',
                    '%' .request('search') . '%'
                );
            }
        }

        if(request()->has('filters') && count($this->filters())) {
            foreach ($this->filters() as $filter) {
                $query = $filter->getQuery($query);
            }
        }

        if(request()->has('order')) {
            $query = $query->orderBy(
                request('order.field'),
                request('order.type')
            );
        } else {
            $query = $query->orderBy(static::$orderField, static::$orderType);
        }

        return $query;
    }
}