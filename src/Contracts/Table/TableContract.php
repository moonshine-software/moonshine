<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Table;

use Closure;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use MoonShine\Fields\Fields;
use MoonShine\ItemActions\ActionButtons;

interface TableContract
{
    public function getFields(): Fields;

    public function rows(): Collection;

    public function getPaginator(): ?LengthAwarePaginator;

    public function hasPaginator(): bool;

    public function getButtons(array $data): ActionButtons;

    public function getBulkButtons(): ActionButtons;

    public function trAttributes(Closure $closure): self;

    public function tdAttributes(Closure $closure): self;
}
