<?php

declare(strict_types=1);

namespace MoonShine\Providers;

use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use MoonShine\Commands\InstallCommand;
use MoonShine\Commands\MakeApplyCommand;
use MoonShine\Commands\MakeComponentCommand;
use MoonShine\Commands\MakeFieldCommand;
use MoonShine\Commands\MakePageCommand;
use MoonShine\Commands\MakeResourceCommand;
use MoonShine\Commands\MakeUserCommand;
use MoonShine\Http\Middleware\ChangeLocale;
use MoonShine\Menu\MenuManager;
use MoonShine\MoonShine;
use MoonShine\MoonShineRegister;
use MoonShine\MoonShineRequest;
use MoonShine\Utilities\AssetManager;

class MoonShineServiceProvider extends ServiceProvider
{
    protected array $commands = [
        InstallCommand::class,
        MakeResourceCommand::class,
        MakeFieldCommand::class,
        MakePageCommand::class,
        MakeUserCommand::class,
        MakeComponentCommand::class,
        MakeApplyCommand::class,
    ];

    protected array $middlewareGroups = [
        'moonshine' => [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            AuthenticateSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
            ChangeLocale::class,
        ],
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->loadAuthConfig();

        $this->registerRouteMiddleware();
    }

    /**
     * Setup auth configuration.
     */
    protected function loadAuthConfig(): void
    {
        $authConfig = collect(config('moonshine.auth', []))
            ->only(['guards', 'providers'])
            ->toArray();

        config(
            Arr::dot($authConfig, 'auth.')
        );
    }

    /**
     * Register the route middleware.
     */
    protected function registerRouteMiddleware(): void
    {
        $this->middlewareGroups['moonshine'] = array_merge(
            $this->middlewareGroups['moonshine'],
            config('moonshine.route.middlewares', [])
        );

        foreach ($this->middlewareGroups as $key => $middleware) {
            app('router')->middlewareGroup($key, $middleware);
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if (config('moonshine.use_migrations', true)) {
            $this->loadMigrationsFrom(MoonShine::path('/database/migrations'));
        }

        $this->publishes([
            MoonShine::path('/config/moonshine.php') => config_path(
                'moonshine.php'
            ),
        ]);

        $this->mergeConfigFrom(
            MoonShine::path('/config/moonshine.php'),
            'moonshine'
        );

        $this->loadTranslationsFrom(MoonShine::path('/lang'), 'moonshine');
        $this->loadRoutesFrom(MoonShine::path('/routes/moonshine.php'));
        $this->loadViewsFrom(MoonShine::path('/resources/views'), 'moonshine');

        $this->publishes([
            MoonShine::path('/public') => public_path('vendor/moonshine'),
        ], ['moonshine-assets', 'laravel-assets']);

        $this->publishes([
            MoonShine::path('/lang') => $this->app->langPath(
                'vendor/moonshine'
            ),
        ]);

        $this->publishes([
            MoonShine::path('/stubs/MoonShineServiceProvider.stub') => app_path(
                'Providers/MoonShineServiceProvider.php'
            ),
        ], 'moonshine-provider');

        if ($this->app->runningInConsole()) {
            $this->commands($this->commands);
        }

        Blade::withoutDoubleEncoding();
        Blade::componentNamespace('MoonShine\Components', 'moonshine');

        $this->app->bind(
            MoonShineRequest::class,
            fn ($app): MoonShineRequest => MoonShineRequest::createFrom($app['request'])
        );

        Blade::directive(
            'moonShineAssets',
            static fn (): string => "<?php echo view('moonshine::layouts.shared.assets') ?>"
        );

        $this->app->singleton(MoonShine::class);
        $this->app->singleton(MenuManager::class);
        $this->app->singleton(AssetManager::class);
        $this->app->singleton(MoonShineRegister::class);
    }
}
