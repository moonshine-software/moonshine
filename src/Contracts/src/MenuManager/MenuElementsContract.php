<?php

declare(strict_types=1);

namespace MoonShine\Contracts\MenuManager;

use Traversable;

interface MenuElementsContract extends Traversable
{
    public function onlyVisible(): self;
}
