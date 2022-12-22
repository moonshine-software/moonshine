<?php

declare(strict_types=1);

namespace Leeto\MoonShine\ViewComponents\Table;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Leeto\MoonShine\Contracts\EntityContract;
use Leeto\MoonShine\Fields\Fields;
use Leeto\MoonShine\ViewComponents\MoonShineViewComponent;

final class Table extends MoonShineViewComponent
{
    protected static string $component = 'TableComponent';

    final public function __construct(
        protected LengthAwarePaginator $paginator,
        protected $fields
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
        $this->paginator->getCollection()->transform(function (EntityContract $values) {
            return TableRow::make($values, $this->fields());
        });

        return $this->paginator;
    }

    public function jsonSerialize(): array
    {
        return [
            ...parent::jsonSerialize(),

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
