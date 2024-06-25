<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Enums;

enum Action: string
{
    case CREATE = 'create';

    case VIEW = 'view';

    case UPDATE = 'update';

    case DELETE = 'delete';

    case MASS_DELETE = 'massDelete';
}
