<?php

declare(strict_types=1);

namespace MoonShine\UI\Contracts\Table;

use Closure;
use MoonShine\Core\Paginator\PaginatorContract;
use MoonShine\UI\Collections\ActionButtons;
use MoonShine\UI\Collections\TableRows;

interface TableContract
{
    public function getRows(): TableRows;

    public function getPaginator(): ?PaginatorContract;

    public function hasPaginator(): bool;

    public function getBulkButtons(): ActionButtons;

    public function trAttributes(Closure $closure): self;

    public function tdAttributes(Closure $closure): self;
}
