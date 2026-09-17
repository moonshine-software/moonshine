<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Enums;

use MoonShine\Support\Enums\Color;

enum TestEnumStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';

    public function toString(): string
    {
        return $this === self::Active ? 'Active subscriber' : 'Inactive subscriber';
    }

    public function getColor(): Color
    {
        return $this === self::Active ? Color::SUCCESS : Color::ERROR;
    }

    public function getIcon(): string
    {
        return $this === self::Active ? 'check' : 'x-mark';
    }
}
