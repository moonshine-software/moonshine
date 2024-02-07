<?php

namespace MoonShine\Support;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;

final class DbOperator
{
    public const DEFAULT_DRIVER = 'mysql';

    public static function getLikeOperator(Model|null $model = null, string $driver = null): string
    {
        $actualDriver = $model ?
            self::getDriverByModel($model) :
            ($driver ?? self::getDefaultDriver());

        return match ($actualDriver) {
            'pgsql' => 'ILIKE',
            default => 'LIKE',
        };
    }

    public static function getDefaultDriver(): string
    {
        $defaultConnection = config('database.default');
        return Arr::get(config('database.connections'), "$defaultConnection.driver", self::DEFAULT_DRIVER);
    }

    public static function getDriverByModel(Model $model): string
    {
        $connection = $model->getConnectionName();

        return Arr::get(config('database.connections'), "$connection.driver", self::DEFAULT_DRIVER);
    }
}
