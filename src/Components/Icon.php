<?php

declare(strict_types=1);

namespace MoonShine\Components;

/** @method static static make(string $icon, int $size = 5, string $color = '', string $class = '') */
final class Icon extends MoonShineComponent
{
    protected string $view = 'moonshine::components.icon';

    public function __construct(
        public string $icon,
        public int $size = 5,
        public string $color = '',
        public string $class = '',
    )
    {
    }
}
