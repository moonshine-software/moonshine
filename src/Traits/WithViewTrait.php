<?php

namespace Leeto\MoonShine\Traits;

trait WithViewTrait
{
    protected static string $view = '';

    public function getView(): string
    {
        return static::$view;
    }
}