<?php

declare(strict_types=1);

namespace MoonShine\Providers;

use Closure;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Router;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Laravel\Octane\Events\RequestHandled;
use MoonShine\AssetManager\AssetManager;
use MoonShine\Commands\InstallCommand;
use MoonShine\Commands\MakeApplyCommand;
use MoonShine\Commands\MakeComponentCommand;
use MoonShine\Commands\MakeControllerCommand;
use MoonShine\Commands\MakeFieldCommand;
use MoonShine\Commands\MakeHandlerCommand;
use MoonShine\Commands\MakeLayoutCommand;
use MoonShine\Commands\MakePageCommand;
use MoonShine\Commands\MakePolicyCommand;
use MoonShine\Commands\MakeResourceCommand;
use MoonShine\Commands\MakeTypeCastCommand;
use MoonShine\Commands\MakeUserCommand;
use MoonShine\Commands\PublishCommand;
use MoonShine\Http\Middleware\ChangeLocale;
use MoonShine\MenuManager\MenuManager;
use MoonShine\MoonShine;
use MoonShine\MoonShineRegister;
use MoonShine\MoonShineRequest;
use MoonShine\MoonShineRouter;
use MoonShine\Theme\ColorManager;

class MoonShineServiceProvider extends ServiceProvider
{
    protected array $commands = [
        InstallCommand::class,
        MakeResourceCommand::class,
        MakeControllerCommand::class,
        MakeFieldCommand::class,
        MakePageCommand::class,
        MakeLayoutCommand::class,
        MakeUserCommand::class,
        MakeComponentCommand::class,
        MakeApplyCommand::class,
        MakeHandlerCommand::class,
        MakeTypeCastCommand::class,
        PublishCommand::class,
        MakePolicyCommand::class,
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
     * Setup auth configuration.
     */
    protected function registerAuthConfig(): self
    {
        $authConfig = collect(config('moonshine.auth', []))
            ->only(['guards', 'providers'])
            ->toArray();

        config(
            Arr::dot($authConfig, 'auth.')
        );

        return $this;
    }

    /**
     * Register the route middleware.
     */
    protected function registerRouteMiddleware(): self
    {
        $this->middlewareGroups['moonshine'] = array_merge(
            $this->middlewareGroups['moonshine'],
            config('moonshine.route.middlewares', [])
        );

        foreach ($this->middlewareGroups as $key => $middleware) {
            app('router')->middlewareGroup($key, $middleware);
        }

        return $this;
    }

    protected function registerBindings(): self
    {
        $this->app->singleton(MoonShine::class);

        $this->app->{app()->runningUnitTests() ? 'bind' : 'singleton'}(
            MoonShineRequest::class,
            fn ($app): MoonShineRequest => MoonShineRequest::createFrom($app['request'])
        );

        $this->app->singleton(MenuManager::class);
        $this->app->singleton(AssetManager::class);
        $this->app->singleton(MoonShineRegister::class);

        $this->app->bind(MoonShineRouter::class);

        $this->app->scoped(ColorManager::class);

        return $this;
    }

    protected function registerBladeDirectives(): self
    {
        $this->callAfterResolving('blade.compiler', static function (BladeCompiler $blade): void {
            $blade->directive(
                'moonShineAssets',
                static fn (): string => "<?php echo view('moonshine::layouts.shared.assets'); ?>"
            );

            $blade->directive(
                'defineEvent',
                static fn ($e): string => "<?php echo MoonShine\Support\AlpineJs::eventBlade($e); ?>"
            );

            $blade->directive(
                'defineEventWhen',
                static fn ($e): string => "<?php echo MoonShine\Support\AlpineJs::eventBladeWhen($e); ?>"
            );
        });

        return $this;
    }

    protected function registerRouterMacro(): self
    {
        Router::macro(
            'moonshine',
            fn (Closure $callback, bool $resource = false) => $this->group(
                moonshine()->configureRoutes(),
                function () use ($callback, $resource): void {
                    Router::group(
                        $resource ? [
                            'prefix' => 'resource/{resourceUri}',
                        ] : [],
                        fn () => $callback($this)
                    );
                }
            )
        );

        return $this;
    }

    public function register(): void
    {
        $this
            ->registerBindings()
            ->registerRouterMacro();
    }

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

        $this->loadTranslationsFrom(MoonShine::path('/lang'), 'moonshine');
        $this->loadRoutesFrom(MoonShine::path('/routes/moonshine.php'));
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'moonshine');

        $this->publishes([
            MoonShine::path('/public') => public_path('vendor/moonshine'),
        ], ['moonshine-assets', 'laravel-assets']);

        $this->publishes([
            MoonShine::path('/lang') => $this->app->langPath(
                'vendor/moonshine'
            ),
        ]);

        $this->mergeConfigFrom(
            MoonShine::path('/config/moonshine.php'),
            'moonshine'
        );

        if ($this->app->runningInConsole()) {
            $this->commands($this->commands);
        }

        Blade::withoutDoubleEncoding();
        Blade::componentNamespace('MoonShine\Components', 'moonshine');

        $this
            ->registerBladeDirectives()
            ->registerRouteMiddleware()
            ->registerAuthConfig();

        tap($this->app['events'], function ($event): void {
            $event->listen(RequestHandled::class, function (RequestHandled $event): void {
                moonshine()->flushState();
            });
        });
    }
}
