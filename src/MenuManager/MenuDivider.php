<?php

declare(strict_types=1);

namespace MoonShine\MenuManager;

use Closure;

/**
 * @method static static make(Closure|string $label = '')
 */
class MenuDivider extends MenuElement
{
    final public function __construct(Closure|string $label = '')
    {
        $this->setLabel($label);
    }
}
