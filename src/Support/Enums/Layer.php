<?php

declare(strict_types=1);

namespace MoonShine\Support\Enums;

enum Layer: string
{
    case TOP = 'topLayer';

    case MAIN = 'mainLayer';

    case BOTTOM = 'bottomLayer';
}
