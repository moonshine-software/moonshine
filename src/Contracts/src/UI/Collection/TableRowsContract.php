<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI\Collection;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use MoonShine\Contracts\UI\TableRowContract;

/**
 * @template-extends Enumerable<array-key, TableRowContract>
 *
 * @mixin Collection<array-key, TableRowContract>
 */
interface TableRowsContract extends Enumerable
{
    public function pushRow(TableCellsContract $cells, int|string|null $key = null, ?Closure $builder = null): self;
}
