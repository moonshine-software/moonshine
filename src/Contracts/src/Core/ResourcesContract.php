<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core;

use Traversable;

/**
 * @template-covariant I of ResourcesContract
 * @template TResource of ResourceContract
 * @mixin I
 *
 * @extends Traversable<array-key, TResource>
 */
interface ResourcesContract extends Traversable
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
