<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use Illuminate\Support\Collection;
use MoonShine\Contracts\Core\Paginator\PaginatorContract;
use MoonShine\Contracts\Core\TypeCasts\CastedDataContract;

interface TableBuilderContract
{
    public function getRows(): TableRowsContract;

    public function getPaginator(bool $async = false): ?PaginatorContract;

    public function getButtons(CastedDataContract $data): ActionButtonsContract;

    public function getItems(): Collection;
}
