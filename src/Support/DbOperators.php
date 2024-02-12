<?php

namespace MoonShine\Support;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;

final class DbOperators
{
    protected string $driver;
    public function __construct(
        ?string $driver = 'mysql'
    ) {
        $this->driver = $driver ?? self::getDefaultDriver();
    }

    public static function byModel(Model $model): self
    {
        $modelDriver = $model->getConnection()->getConfig('driver');

        return new self(
            $modelDriver
        );
    }

    public function like(): string
    {
        return match ($this->driver) {
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
