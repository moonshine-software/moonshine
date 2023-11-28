<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures;

use Illuminate\Support\ServiceProvider;
use MoonShine\MoonShineRequest;

class TestServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->bind(
            MoonShineRequest::class,
            fn ($app): MoonShineRequest => MoonShineRequest::createFrom($app['request'])
        );

        $this->loadMigrationsFrom(realpath('./tests/Fixtures/Migrations'));
    }
}
