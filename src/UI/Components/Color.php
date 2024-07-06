<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use MoonShine\Support\Enums\Color as ColorEnum;

/** @method static static make(string|ColorEnum $color) */
final class Color extends MoonShineComponent
{
    protected string $view = 'moonshine::components.color';

    public function __construct(public string|ColorEnum $color)
    {
        $this->color = $this->color instanceof ColorEnum ? $this->color->value : $this->color;

        parent::__construct();
    }
}
