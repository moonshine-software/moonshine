<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Enums;

enum TestEnumBreadcrumbLabel: string
{
    case Blue = 'blue';

    public function toString(): string
    {
        return 'Blue label';
    }
}
