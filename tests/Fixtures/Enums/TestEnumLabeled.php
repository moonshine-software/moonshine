<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Enums;

enum TestEnumLabeled: string
{
    case Web = 'web';
    case Mobile = 'mobile';

    public function toString(): string
    {
        return match ($this) {
            self::Web => 'Web platform',
            self::Mobile => 'Mobile app',
        };
    }
}
