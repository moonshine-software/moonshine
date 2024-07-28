<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use Closure;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use Traversable;

interface TableCellsContract extends Traversable
{
    public function pushFields(FieldsContract $fields, ?Closure $builder = null): self;

    public function pushCell(Closure|string $content, ?int $index = null, ?Closure $builder = null): self;

    public function pushWhen(Closure|bool $condition, Closure $value): self;

    public function pushCellWhen(Closure|bool $condition, Closure|string $content, ?int $index = null, ?Closure $builder = null): self;
}
