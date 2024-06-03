<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Table;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Support\Traits\Makeable;
use MoonShine\UI\Traits\WithViewRenderer;
use Throwable;

/**
 * @internal
 * @method static static make(TableBuilder $table, string|int $key, int $index = 0)
 */
final class TableRowRenderer
{
    use Makeable;
    use WithViewRenderer;

    public function __construct(
        private TableBuilder $table,
        private string|int $key,
        private int $index = 0
    ) {
    }

    /**
     * @throws Throwable
     */
    protected function resolveRender(): View|Closure|string
    {
        $class = $this->table->hasCast()
            ? new ($this->table->getCast()->getClass())
            : null;

        if ($class instanceof Model) {
            $item = $class::query()->find($this->key);
        } else {
            $item = $this->table->rows()->first(
                fn (TableRow $row): bool => $row->getKey() === $this->key
            )?->toArray() ?? [];
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
