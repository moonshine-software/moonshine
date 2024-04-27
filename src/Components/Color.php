<?php

declare(strict_types=1);

namespace MoonShine\Components;

/** @method static static make(string $color) */
final class Color extends MoonShineComponent
{
    protected string $view = 'moonshine::components.color';

    public function __construct(public string $color)
    {
        parent::__construct();
    }
}
