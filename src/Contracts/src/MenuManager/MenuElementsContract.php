<?php

declare(strict_types=1);

namespace MoonShine\Contracts\MenuManager;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;

/**
 * @template-extends Enumerable<array-key, MenuElementContract>
 *
 * @mixin Collection
 */
interface MenuElementsContract extends Enumerable
{
    public function onlyVisible(): self;

    public function topMode(?Closure $condition = null): self;
}
