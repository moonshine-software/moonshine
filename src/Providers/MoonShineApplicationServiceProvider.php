<?php

declare(strict_types=1);

namespace MoonShine\Providers;

use Illuminate\Support\ServiceProvider;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Menu\MenuSection;
use MoonShine\MoonShine;
use Throwable;

class MoonShineApplicationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     * @throws Throwable
     */
    public function boot(): void
    {
        MoonShine::resources($this->resources());
        MoonShine::menu($this->menu());
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * @return array<ResourceContract|string>
     */
    protected function resources(): array
    {
        return [];
    }

    /**
     * @return array<MenuSection>
     */
    protected function menu(): array
    {
        return [];
    }
}