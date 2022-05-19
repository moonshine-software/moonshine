<?php

namespace Leeto\MoonShine\Providers;

use Illuminate\Support\Facades\Blade;
use Leeto\MoonShine\Commands\UserCommand;
use Leeto\MoonShine\Commands\InstallCommand;
use Leeto\MoonShine\Commands\ResourceCommand;
use Leeto\MoonShine\Components\MenuComponent;
use Leeto\MoonShine\Extensions\BaseExtension;
use Leeto\MoonShine\Menu\Menu;
use Leeto\MoonShine\Middleware\Authenticate;
use Leeto\MoonShine\Middleware\Session;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Leeto\MoonShine\MoonShine;

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
        $this->loadMigrationsFrom(__DIR__. '/../database/migrations');
        $this->loadTranslationsFrom(__DIR__. '/../lang', 'moonshine');
        $this->loadViewsFrom(__DIR__. '/../views', 'moonshine');

        $this->publishes([
            __DIR__ . '/../config/moonshine.php' => config_path('moonshine.php'),
        ]);

        $this->mergeConfigFrom(
            __DIR__.'/../config/moonshine.php', 'moonshine'
        );

        $this->publishes([
            __DIR__. '/../assets' => public_path('vendor/moonshine'),
        ], 'public');

        $this->publishes([
            __DIR__. '/../lang' => $this->app->langPath('vendor/moonshine'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands($this->commands);
        }

        Blade::withoutDoubleEncoding();
        Blade::componentNamespace('Leeto\\MoonShine\\Components', 'moonshine');
        Blade::component('menu-component', MenuComponent::class);

        $this->app->singleton(MoonShine::class, function ($app) {
            return new MoonShine();
        });

        $this->app->singleton(Menu::class, function ($app) {
            return new Menu();
        });

        $extensions = [];

        if(config("moonshine.extensions")) {
            foreach (config("moonshine.extensions") as $class) {
                $extensions[] = new $class();
            }
        }

        $this->app->bind(BaseExtension::class, function ($app) use ($extensions) {
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
