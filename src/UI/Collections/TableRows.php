<?php

declare(strict_types=1);

namespace MoonShine\UI\Collections;

use Illuminate\Support\Collection;
use MoonShine\UI\Components\Table\TableRow;
use Closure;

/**
 * @template TKey of array-key
 *
 * @extends Collection<TKey, TableRow>
 */
final class TableRows extends Collection
{
    public function pushRow(TableCells $cells, ?int $index, ?Closure $builder = null): self
    {
        return $this->push(
            TableRow::make(
                $cells,
                $index
            )->when(
                !is_null($builder),
                fn(TableRow $tr) => $builder($tr)
            )
        );
    }
}
