<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use MoonShine\Contracts\UI\Collection\TableCellsContract;

interface TableRowContract extends ComponentContract
{
    public function hasKey(): bool;

    public function setKey(int|string|null $value): self;

    public function getKey(): int|string|null;

    public function getCells(): TableCellsContract;
}
