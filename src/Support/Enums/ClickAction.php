<?php

declare(strict_types=1);

namespace MoonShine\Support\Enums;

enum ClickAction: string
{
    case SELECT = 'select';

    case EDIT = 'edit';

    case DETAIL = 'detail';
}
