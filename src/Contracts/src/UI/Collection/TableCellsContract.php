<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI\Collection;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\UI\TableCellContract;

/**
 * @template-extends Enumerable<array-key, TableCellContract>
 *
 * @mixin Collection<array-key, TableCellContract>
 */
interface TableCellsContract extends Enumerable
{
    public function pushFields(FieldsContract $fields, ?Closure $builder = null, int $startIndex = 0): self;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function pushCell(Closure|string $content, ?int $index = null, ?Closure $builder = null, array $attributes = []): self;

    public function pushWhen(Closure|bool $condition, Closure $value): self;

    public function pushCellWhen(Closure|bool $condition, Closure|string $content, ?int $index = null, ?Closure $builder = null): self;
}
