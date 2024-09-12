<?php

declare(strict_types=1);

namespace MoonShine\Contracts\MenuManager;

use Traversable;

/**
 * @extends Traversable<array-key, MenuElementContract>
 */
interface MenuElementsContract extends Traversable
{
    public function onlyVisible(): self;
}
