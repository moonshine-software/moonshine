<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Enums;

enum Ability: string
{
    case CREATE = 'create';

    case VIEW = 'view';

    case VIEW_ANY = 'viewAny';

    case UPDATE = 'update';

    case DELETE = 'delete';

    case MASS_DELETE = 'massDelete';

    case RESTORE = 'restore';

    case FORCE_DELETE = 'forceDelete';
}
