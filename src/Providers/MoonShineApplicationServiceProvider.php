<?php

declare(strict_types=1);

namespace MoonShine\Providers;

use Illuminate\Support\ServiceProvider;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Menu\Menu;
use MoonShine\Menu\MenuSection;
use MoonShine\MoonShine;
use MoonShine\Utilities\AssetManager;
use Throwable;

class MoonShineApplicationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @throws Throwable
     */
    public function boot(): void
    {
        MoonShine::resources($this->resources());
        MoonShine::menu($this->menu());

        Menu::register(MoonShine::getMenu());

        MoonShine::resolveRoutes();

        $theme = $this->theme();

        moonshineAssets()->when(
            !empty($theme['css']),
            static fn(AssetManager $assets) => $assets->mainCss($theme['css'])
        )->when(
            !empty($theme['colors']),
            static fn(AssetManager $assets) => $assets->colors($theme['colors'])
        );
    }

    /**
     * Register any application services.
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

    protected function theme(): array
    {
        return [];
    }
}
