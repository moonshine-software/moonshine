<?php

declare(strict_types=1);

namespace MoonShine\Support\Enums;

enum FlashType: string
{
    case DEFAULT = 'default';

    case PRIMARY = 'primary';

    case SECONDARY = 'secondary';

    case SUCCESS = 'success';

    case ERROR = 'error';

    case WARNING = 'warning';

    case INFO = 'info';
}
