<?php

declare(strict_types=1);

namespace MoonShine\Support\Enums;

enum Env: string
{
    case PRODUCTION = 'production';

    case LOCAL = 'local';

    case TESTING = 'testing';

    case CONSOLE = 'console';

    public static function fromString(string $env): self
    {
        return match ($env) {
            'production' => self::PRODUCTION,
            'testing' => self::TESTING,
            'console' => self::CONSOLE,
            default => self::LOCAL,
        };
    }
}
