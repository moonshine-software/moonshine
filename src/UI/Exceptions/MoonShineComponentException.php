<?php

declare(strict_types=1);

namespace MoonShine\UI\Exceptions;

use MoonShine\Core\Exceptions\MoonShineException;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;

class MoonShineComponentException extends MoonShineException
{
    public static function tabsAreNotRendering(): static
    {
        return new static('Can`t render. You need to use ' . Tabs::class . ' class');
    }

    public static function onlyTabAllowed(): static
    {
        return new static('Tab must be a class of ' . Tab::class);
    }
}
