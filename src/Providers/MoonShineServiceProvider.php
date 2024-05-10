<?php

declare(strict_types=1);

namespace MoonShine\Providers;

use Closure;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use Laravel\Octane\Events\RequestHandled;
use MoonShine\Applies\AppliesRegister;
use MoonShine\AssetManager\AssetManager;
use MoonShine\ColorManager\ColorManager;
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
use MoonShine\MenuManager\MenuManager;
use MoonShine\Models\MoonshineUser;
use MoonShine\MoonShine;
use MoonShine\MoonShineConfigurator;
use MoonShine\MoonShineRequest;
use MoonShine\MoonShineRouter;

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

    /**
     * Setup auth configuration.
     */
    protected function registerAuth(): self
    {
        Config::set('auth.guards.moonshine', [
            'driver' => 'session',
            'provider' => 'moonshine',
        ]);

        Config::set('auth.providers.moonshine', [
            'driver' => 'eloquent',
            'model' => MoonshineUser::class,
        ]);

        return $this;
    }

    /**
     * Register the route middleware.
     */
    protected function registerRouteMiddleware(): self
    {
        app('router')->middlewareGroup('moonshine', [
            ...moonshineConfig()->getMiddlewares(),
        ]);

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
        $this->app->singleton(AppliesRegister::class);
        $this->app->singleton(MoonShineConfigurator::class);

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
                moonshineConfig()->getDefaultRouteGroup(),
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

        $this->mergeConfigFrom(
            MoonShine::path('/config/moonshine.php'),
            'moonshine'
        );
    }

    public function boot(): void
    {
        if (moonshineConfig()->isUseMigrations()) {
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

        if ($this->app->runningInConsole()) {
            $this->commands($this->commands);
        }

        Blade::withoutDoubleEncoding();
        Blade::componentNamespace('MoonShine\Components', 'moonshine');

        $this
            ->registerBladeDirectives()
            ->registerRouteMiddleware()
            ->registerAuth();

        tap($this->app['events'], function ($event): void {
            $event->listen(RequestHandled::class, function (RequestHandled $event): void {
                moonshine()->flushState();
            });
        });
    }
}
