<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits;

trait WithComponent
{
    protected static string $component = '';

    public function getComponent(): string
    {
        return static::$component;
    }
}
