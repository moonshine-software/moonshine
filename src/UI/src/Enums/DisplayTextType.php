<?php

declare(strict_types=1);

namespace MoonShine\UI\Enums;

enum DisplayTextType: string
{
    case LABEL = 'label';

    case HINT = 'hint';

    case PREFIX = 'prefix';

    case SUFFIX = 'suffix';
}
