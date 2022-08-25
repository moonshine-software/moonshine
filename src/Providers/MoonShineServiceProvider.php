<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Providers;

use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Laravel\Sanctum\SanctumServiceProvider;
use Leeto\MoonShine\Commands\InstallCommand;
use Leeto\MoonShine\Commands\ResourceCommand;
use Leeto\MoonShine\Commands\UserCommand;
use Leeto\MoonShine\Dashboard\Dashboard;
use Leeto\MoonShine\Http\Middleware\ConfigureSanctum;
use Leeto\MoonShine\Menu\Menu;
use Leeto\MoonShine\Models\MoonshineUser;
use Leeto\MoonShine\MoonShine;
use Leeto\MoonShine\Utilities\AssetManager;

class MoonShineServiceProvider extends ServiceProvider
{
    protected array $commands = [
        InstallCommand::class,
        ResourceCommand::class,
        UserCommand::class,
    ];

    protected array $middlewareGroups = [
        'moonshine' => [
            StartSession::class,
            ConfigureSanctum::class,
            EnsureFrontendRequestsAreStateful::class,
            'throttle:60,1',
            SubstituteBindings::class,
        ],
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->resolveAuth();

        $this->registerRouteMiddleware();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(MoonShine::path('/database/migrations'));
        $this->loadTranslationsFrom(MoonShine::path('/lang'), 'moonshine');

        $this->publishes([
            MoonShine::path('/config/moonshine.php') => config_path('moonshine.php'),
        ]);

        $this->mergeConfigFrom(
            MoonShine::path('/config/moonshine.php'),
            'moonshine'
        );

        $this->loadRoutesFrom(MoonShine::path('/routes/moonshine.php'));

        $this->publishes([
            MoonShine::path('/lang') => $this->app->langPath('vendor/moonshine'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands($this->commands);
        }

        $this->app->singleton(MoonShine::class, fn() => new MoonShine());
        $this->app->singleton(Menu::class, fn() => new Menu());
        $this->app->singleton(Dashboard::class, fn() => new Dashboard());
        $this->app->singleton(AssetManager::class, fn() => new AssetManager());

        $this->app->register(SanctumServiceProvider::class);
    }

    protected function resolveAuth(): void
    {
        config()->set('auth.guards.moonshine', [
            'driver' => 'session',
            'provider' => 'moonshine',
        ]);

        config()->set('auth.providers.moonshine', [
            'driver' => 'eloquent',
            'model' => MoonshineUser::class,
        ]);
    }

    /**
     * Register the route middleware.
     *
     * @return void
     */
    protected function registerRouteMiddleware(): void
    {
        foreach ($this->middlewareGroups as $key => $middleware) {
            app('router')->middlewareGroup($key, $middleware);
        }
    }
}
