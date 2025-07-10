<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core;

use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use MoonShine\Support\Enums\PageType;

/**
 * @template TPage of PageContract = PageContract
 *
 * @template-extends Enumerable<array-key, TPage>
 *
 * @mixin Collection<array-key, TPage>
 */
interface PagesContract extends Enumerable
{
    /**
     * @param  null|TPage  $default
     * @return null|TPage
     */
    public function findByType(
        PageType $type,
        ?PageContract $default = null
    ): ?PageContract;

    /**
     * @template T of TPage
     * @param  class-string<T>  $class
     * @param  null|TPage  $default
     *
     * @return null|T
     */
    public function findByClass(
        string $class,
        ?PageContract $default = null
    ): ?PageContract;

    /**
     * @param  null|TPage  $default
     * @return null|TPage
     */
    public function findByUri(
        string $uri,
        ?PageContract $default = null
    ): ?PageContract;

    /**
     * @return null|TPage
     */
    public function activePage(): ?PageContract;
}
