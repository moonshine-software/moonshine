<?php

namespace Leeto\MoonShine\Traits;

trait WithView
{
    protected static string $view = '';

    public function getView(): string
    {
        return static::$view;
    }
}
