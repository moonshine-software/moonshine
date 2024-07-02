<?php

declare(strict_types=1);

namespace MoonShine\UI\Collections;

use Closure;
use Illuminate\Support\Collection;
use MoonShine\UI\Components\Table\TableRow;

/**
 * @template TKey of array-key
 *
 * @extends Collection<TKey, TableRow>
 */
final class TableRows extends Collection
{
    public function pushRow(TableCells $cells, int|string|null $key, ?Closure $builder = null): self
    {
        return $this->push(
            TableRow::make(
                $cells,
                $key
            )->when(
                ! is_null($builder),
                static fn (TableRow $tr) => $builder($tr)
            )
        );
    }
}
