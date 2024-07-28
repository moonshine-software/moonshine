<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core;

use Traversable;

interface ResourcesContract extends Traversable
{
    public function findByUri(
        string $uri,
        ResourceContract $default = null
    ): ?ResourceContract;

    public function findByClass(
        string $class,
        ResourceContract $default = null
    ): ?ResourceContract;
}
