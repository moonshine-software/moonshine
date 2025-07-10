<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core;

use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;

/**
 * @template TResource of ResourceContract = ResourceContract
 *
 * @template-extends Enumerable<array-key, TResource>
 *
 * @mixin Collection<array-key, TResource>
 */
interface ResourcesContract extends Enumerable
{
    /**
     * @param  null|TResource $default
     *
     * @return null|TResource
     */
    public function findByUri(
        string $uri,
        ?ResourceContract $default = null
    ): ?ResourceContract;

    /**
     * @param  class-string<TResource>  $default
     * @param  null|TResource  $default
     *
     * @return null|TResource
     */
    public function findByClass(
        string $class,
        ?ResourceContract $default = null
    ): ?ResourceContract;
}
