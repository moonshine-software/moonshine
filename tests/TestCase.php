<?php

namespace Leeto\MoonShine\Tests;

use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Leeto\MoonShine\Providers\MoonShineServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    use InteractsWithViews;

    protected function getPackageProviders($app): array
    {
        return [
            MoonShineServiceProvider::class,
        ];
    }
}