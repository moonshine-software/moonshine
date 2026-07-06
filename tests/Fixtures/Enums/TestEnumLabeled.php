<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Enums;

enum TestEnumLabeled: string
{
    case Web = 'web-platform';
    case Mobile = 'mobile-app';
}
