<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures;

use Illuminate\Support\ServiceProvider;

class TestServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(realpath('./tests/Fixtures/Migrations'));
    }
}