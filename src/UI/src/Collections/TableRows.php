<?php

declare(strict_types=1);

namespace MoonShine\UI\Collections;

use Closure;
use Illuminate\Support\Collection;
use MoonShine\Contracts\UI\Collection\TableCellsContract;
use MoonShine\Contracts\UI\Collection\TableRowsContract;
use MoonShine\Contracts\UI\TableRowContract;
use MoonShine\UI\Components\Table\TableRow;

/**
 * @extends Collection<array-key, TableRowContract>
 */
final class TableRows extends Collection implements TableRowsContract
{
    public function pushRow(TableCellsContract $cells, int|string|null $key = null, ?Closure $builder = null): self
    {
        return $this->push(
            TableRow::make(
                $cells,
                $key
            )->when(
                ! \is_null($builder),
                static fn (TableRowContract $tr) => $builder($tr)
            )
        );
    }
}
