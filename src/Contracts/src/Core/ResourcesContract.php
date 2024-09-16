<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core;

use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;

/**
 * @template-covariant I of ResourcesContract
 * @template TResource of ResourceContract
 * @mixin I
 *
 * @template-extends Enumerable<array-key, TResource>
 *
 * @mixin Collection
 */
interface ResourcesContract extends Enumerable
{
    /**
     * @param  ?ResourceContract<TResource>  $default
     *
     * @return ?ResourceContract<TResource>
     */
    public function findByUri(
        string $uri,
        ResourceContract $default = null
    ): ?ResourceContract;

    /**
     * @param  ?ResourceContract<TResource>  $default
     *
     * @return ?ResourceContract<TResource>
     */
    public function findByClass(
        string $class,
        ResourceContract $default = null
    ): ?ResourceContract;
}
