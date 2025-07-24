<?php

declare(strict_types=1);

namespace MoonShine\Support\Enums;

enum ListRowEventType: string
{
    case APPEND = 'append';

    case PREPEND = 'prepend';

    case REMOVE = 'remove';

    case CHANGE = 'change';
}
