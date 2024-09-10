<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core;

use MoonShine\Support\Enums\PageType;
use Traversable;

/**
 * @template-covariant I of PagesContract
 * @template-covariant TPage of PageContract
 * @mixin I
 *
 * @template-implements Traversable<array-key, TPage>
 */
interface PagesContract extends Traversable
{
    /**
     * @param  PageType  $type
     * @param  ?PageContract<TPage>  $default
     *
     * @return ?PageContract<TPage>
     */
    public function findByType(
        PageType $type,
        PageContract $default = null
    ): ?PageContract;

    /**
     * @template T of PageContract
     * @param  class-string<T>  $class
     * @param  ?PageContract<TPage>  $default
     *
     * @return ?PageContract<T>
     */
    public function findByClass(
        string $class,
        PageContract $default = null
    ): ?PageContract;

    /**
     * @param  string $uri
     * @param  ?PageContract<TPage>  $default
     *
     * @return ?PageContract<TPage>
     */
    public function findByUri(
        string $uri,
        PageContract $default = null
    ): ?PageContract;
}
