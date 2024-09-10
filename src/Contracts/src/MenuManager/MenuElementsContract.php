<?php

declare(strict_types=1);

namespace MoonShine\Contracts\MenuManager;

use Traversable;

/**
 * @template-implements Traversable<array-key, MenuElementContract>
 */
interface MenuElementsContract extends Traversable
{
    public function onlyVisible(): self;
}
