<?php

declare(strict_types=1);

namespace MoonShine\Contracts;

use MoonShine\Components\Layout\LayoutBuilder;

interface MoonShineLayoutContract
{
    public static function build(): LayoutBuilder;
}
