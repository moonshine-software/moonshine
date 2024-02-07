<?php

namespace MoonShine\Support;

use Illuminate\Support\Arr;

final class DbOperator
{
    public static function getLikeOperator(string $driver = null): string
    {
        $actualDriver = $driver ?? self::getDefaultDriver();

        return match ($actualDriver) {
            'pgsql' => 'ILIKE',
            default => 'LIKE',
        };
    }

    public static function getDefaultDriver(): string
    {
        $defaultConnection = config('database.default');
        return Arr::get(config('database.connections'), "$defaultConnection.driver", 'mysql');
    }
}
