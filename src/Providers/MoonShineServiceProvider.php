<?php

namespace Leeto\MoonShine\Providers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Leeto\MoonShine\Commands\InstallCommand;
use Leeto\MoonShine\Commands\ResourceCommand;
use Leeto\MoonShine\Commands\UserCommand;
use Leeto\MoonShine\Components\MenuComponent;
use Leeto\MoonShine\Dashboard\Dashboard;
use Leeto\MoonShine\Extensions\Extension;
use Leeto\MoonShine\Http\Middleware\Authenticate;
use Leeto\MoonShine\Http\Middleware\Session;
use Leeto\MoonShine\Menu\Menu;
use Leeto\MoonShine\MoonShine;
use Leeto\MoonShine\Utilities\AssetManager;

class MoonShineServiceProvider extends ServiceProvider
{
    protected array $commands = [
        InstallCommand::class,
        ResourceCommand::class,
        UserCommand::class,
    ];

    protected array $routeMiddleware = [
        'moonshine.auth' => Authenticate::class,
        'moonshine.session' => Session::class,
    ];

    protected array $middlewareGroups = [
        'moonshine' => [
            'moonshine.auth',
        ],
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->loadAuthConfig();

        $this->registerRouteMiddleware();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(MoonShine::path('/database/migrations'));
        $this->loadTranslationsFrom(MoonShine::path('/lang'), 'moonshine');
        $this->loadViewsFrom(MoonShine::path('/resources/views'), 'moonshine');

        $this->publishes([
            MoonShine::path('/config/moonshine.php') => config_path('moonshine.php'),
        ]);

        $this->mergeConfigFrom(
            MoonShine::path('/config/moonshine.php'), 'moonshine'
        );

        $this->publishes([
            MoonShine::path('/public') => public_path('vendor/moonshine'),
        ], ['moonshine-assets', 'laravel-assets']);

        $this->publishes([
            MoonShine::path('/lang') => $this->app->langPath('vendor/moonshine'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands($this->commands);
        }

        Blade::withoutDoubleEncoding();
        Blade::componentNamespace('Leeto\MoonShine\Components', 'moonshine');
        Blade::component('menu-component', MenuComponent::class);

        $this->app->singleton(MoonShine::class, function ($app) {
            return new MoonShine();
        });

        $this->app->singleton(Menu::class, function ($app) {
            return new Menu();
        });

        $this->app->singleton(Dashboard::class, function ($app) {
            return new Dashboard();
        });

        $this->app->singleton(AssetManager::class, function ($app) {
            return new AssetManager();
        });

        $extensions = [];

        if(config('moonshine.extensions')) {
            foreach (config('moonshine.extensions') as $class) {
                $extensions[] = new $class();
            }
        }

        $this->app->bind(Extension::class, function ($app) use ($extensions) {
           return $extensions;
        });
    }

    /**
     * Setup auth configuration.
     *
     * @return void
     */
    protected function loadAuthConfig()
    {
        config(Arr::dot(config('moonshine.auth', []), 'auth.'));
    }

    /**
     * Register the route middleware.
     *
     * @return void
     */
    protected function registerRouteMiddleware()
    {
        // register route middleware.
        foreach ($this->routeMiddleware as $key => $middleware) {
            app('router')->aliasMiddleware($key, $middleware);
        }

        // register middleware group.
        foreach ($this->middlewareGroups as $key => $middleware) {
            app('router')->middlewareGroup($key, $middleware);
        }
    }
}
