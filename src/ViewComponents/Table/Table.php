<?php

declare(strict_types=1);

namespace Leeto\MoonShine\ViewComponents\Table;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use JsonSerializable;
use Leeto\MoonShine\Contracts\ValueEntityContract;
use Leeto\MoonShine\Contracts\ViewComponentContract;
use Leeto\MoonShine\Fields\Fields;
use Leeto\MoonShine\Traits\Makeable;
use Leeto\MoonShine\Traits\WithComponentAttributes;

final class Table implements ViewComponentContract, JsonSerializable
{
    use Makeable;
    use WithComponentAttributes;

    public function __construct(
        protected LengthAwarePaginator $paginator,
        protected Fields $fields
    ) {
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
        $this->paginator->getCollection()->transform(function (ValueEntityContract $values) {
            return TableRow::make($values, $this->fields());
        });

        return $this->paginator;
    }

    public function jsonSerialize(): array
    {
        return [
            'paginator' => collect($this->paginator())->only([
                'current_page',
                'from',
                'last_page',
                'per_page',
                'to',
                'total'
            ]),
            'rows' => $this->resolveFieldsPaginator()->items(),
            'columns' => $this->columns(),
        ];
    }
}
