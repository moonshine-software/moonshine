<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use Closure;
use Traversable;

interface TableRowsContract extends Traversable
{
    public function pushRow(TableCellsContract $cells, int|string|null $key, ?Closure $builder = null): self;
}
