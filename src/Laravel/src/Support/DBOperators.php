<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Support;

use Illuminate\Database\Eloquent\Model;

final class DBOperators
{
    protected string $driver;

    public function __construct(
        ?string $driver = null
    ) {
        $this->driver = $driver ?? self::getDefaultDriver();
    }

    public static function byModel(Model $model): self
    {
        $modelDriver = $model->getConnection()->getDriverName();

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
        /** @var string $defaultConnection */
        $defaultConnection = config('database.default');

        /** @var string */
        return config("database.connections.$defaultConnection.driver", 'mysql');
    }
}
