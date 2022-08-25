<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Table;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use JsonSerializable;
use Leeto\MoonShine\Fields\Fields;
use Leeto\MoonShine\Resources\Resource;
use Leeto\MoonShine\Traits\Makeable;
use Leeto\MoonShine\Traits\WithComponentAttributes;

class Table implements JsonSerializable
{
    use Makeable;
    use WithComponentAttributes;

    public function __construct(
        protected Resource $resource,
        protected LengthAwarePaginator $paginator,
        protected Fields $fields
    ) {
    }

    public function resource(): Resource
    {
        return $this->resource;
    }

    public function fields(): Fields
    {
        return $this->fields;
    }

    public function columns(): TableHead
    {
        return TableHead::make($this->fields());
    }

    public function paginator(): LengthAwarePaginator
    {
        return $this->paginator;
    }

    public function resolveFieldsPaginator(): LengthAwarePaginator
    {
        $this->paginator->getCollection()->transform(function (Model $values) {
            return TableRow::make($this->resource(), $values, $this->fields());
        });

        return $this->paginator;
    }

    public function jsonSerialize(): array
    {
        return [
            'paginator' => collect($this->paginator())->except(['data']),
            'rows' => $this->resolveFieldsPaginator()->items(),
            'columns' => $this->columns(),
        ];
    }
}
