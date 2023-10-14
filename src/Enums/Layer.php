<?php

declare(strict_types=1);

namespace MoonShine\Enums;

enum Layer: string
{
    case TOP = 'topLayer';

    case MAIN = 'mainLayer';

    case BOTTOM = 'bottomLayer';
}
