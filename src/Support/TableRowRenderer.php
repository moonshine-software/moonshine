<?php

declare(strict_types=1);

namespace MoonShine\Support;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Components\TableBuilder;
use MoonShine\Table\TableRow;
use MoonShine\Traits\Makeable;
use Throwable;

/**
 * @method static static make(TableBuilder $table, string|int $key, int $index = 0)
 */
final class TableRowRenderer
{
    use Makeable;

    public function __construct(
        private TableBuilder $table,
        private string|int $key,
        private int $index = 0
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function render(): View|string
    {
        $class = $this->table->hasCast()
            ? new ($this->table->getCast()->getClass())
            : null;

        if ($class instanceof Model) {
            $item = $class::query()->find($this->key);
        } else {
            $item = $this->table->rows()->first(
                fn (TableRow $row): bool => $row->getKey() === $this->key
            )->toArray() ?? [];
        }

        if (blank($item)) {
            return '';
        }

        return $this->table
            ->items(
                array_filter([
                    $item,
                ])
            )
            ->performBeforeRender()
            ->rows()
            ->first()
            ->setIndex($this->index)
            ->mapTableStates($this->table)
            ->render();
    }
}
