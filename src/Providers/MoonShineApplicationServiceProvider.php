<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Providers;

use Illuminate\Support\ServiceProvider;

class MoonShineApplicationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->resources();
        $this->menu();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    protected function resources(): void
    {
        //
    }

    protected function menu(): void
    {
        //
    }
}
