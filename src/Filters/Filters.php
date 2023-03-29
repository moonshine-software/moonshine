<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Filters;

use Illuminate\Support\Collection;
use Throwable;

final class Filters extends Collection
{
    /**
     * @param  string  $column
     * @param  Filter|null  $default
     * @return ?Filter
     * @throws Throwable
     */
    public function findFilterByColumn(string $column, Filter $default = null): ?Filter
    {
        return $this->first(static function (Filter $field) use ($column) {
            return $field->field() === $column;
        }, $default);
    }
}
