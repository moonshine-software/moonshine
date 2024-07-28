<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core;

use MoonShine\Support\Enums\PageType;
use Traversable;

interface PagesContract extends Traversable
{
    public function findByType(
        PageType $type,
        PageContract $default = null
    ): ?PageContract;

    public function findByClass(
        string $class,
        PageContract $default = null
    ): ?PageContract;

    public function findByUri(
        string $uri,
        PageContract $default = null
    ): ?PageContract;
}
