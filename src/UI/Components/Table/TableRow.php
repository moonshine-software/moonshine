<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Table;

use MoonShine\UI\Collections\TableCells;
use MoonShine\UI\Components\MoonShineComponent;

/**
 * @method static static make(TableCells $cells, ?int $index = null)
 */
final class TableRow extends MoonShineComponent
{
    protected string $view = 'moonshine::components.table.row';

    public function __construct(
        protected TableCells $cells,
        protected int|string|null $key = null
    ) {
        parent::__construct();
    }

    public function hasKey(): bool
    {
        return ! is_null($this->key);
    }

    public function setKey(int|string|null $value): self
    {
        $this->key = $value;

        return $this;
    }

    public function getKey(): int
    {
        return $this->key;
    }

    public function getCells(): TableCells
    {
        return $this->cells;
    }

    protected function prepareBeforeRender(): void
    {
        $this->customAttributes([
            'data-row-key' => $this->hasKey() ? $this->getKey() : null,
        ]);
    }

    protected function viewData(): array
    {
        return [
            'cells' => $this->getCells(),
        ];
    }
}
