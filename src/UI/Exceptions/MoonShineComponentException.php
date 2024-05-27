<?php

declare(strict_types=1);

namespace MoonShine\UI\Exceptions;

use MoonShine\Core\Exceptions\MoonShineException;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Components\Tabs\Tabs;

class MoonShineComponentException extends MoonShineException
{
    public static function tabsAreNotRendering(): self
    {
        return new self('Can`t render. You need to use ' . Tabs::class . ' class');
    }

    public static function onlyTabAllowed(): self
    {
        return new self('Tab must be a class of ' . Tab::class);
    }
}
