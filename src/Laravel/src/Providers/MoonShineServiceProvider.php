<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Providers;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use MoonShine\AssetManager\AssetManager;
use MoonShine\ColorManager\ColorManager;
use MoonShine\Contracts\AssetManager\AssetManagerContract;
use MoonShine\Contracts\AssetManager\AssetResolverContract;
use MoonShine\Contracts\ColorManager\ColorManagerContract;
use MoonShine\Contracts\Core\DependencyInjection\AppliesRegisterContract;
use MoonShine\Contracts\Core\DependencyInjection\CacheAttributesContract;
use MoonShine\Contracts\Core\DependencyInjection\ConfiguratorContract;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\Core\DependencyInjection\CrudRequestContract;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\DependencyInjection\OptimizerCollectionContract;
use MoonShine\Contracts\Core\DependencyInjection\RequestContract;
use MoonShine\Contracts\Core\DependencyInjection\RouterContract;
use MoonShine\Contracts\Core\DependencyInjection\StorageContract;
use MoonShine\Contracts\Core\DependencyInjection\TranslatorContract;
use MoonShine\Contracts\Core\DependencyInjection\ViewRendererContract;
use MoonShine\Contracts\MenuManager\MenuAutoloaderContract;
use MoonShine\Contracts\MenuManager\MenuManagerContract;
use MoonShine\Core\Collections\OptimizerCollection;
use MoonShine\Core\Core;
use MoonShine\Core\Support\CacheAttributes;
use MoonShine\Crud\Contracts\Notifications\MoonShineNotificationContract;
use MoonShine\Crud\Contracts\Page\DetailPageContract;
use MoonShine\Crud\Contracts\Page\FormPageContract;
use MoonShine\Crud\Contracts\Page\IndexPageContract;
use MoonShine\Crud\Notifications\MemoryNotification;
use MoonShine\Laravel\Applies\Fields\FileModelApply;
use MoonShine\Laravel\Applies\Filters\BelongsToManyModelApply;
use MoonShine\Laravel\Applies\Filters\BelongsToModelApply;
use MoonShine\Laravel\Applies\Filters\CheckboxModelApply;
use MoonShine\Laravel\Applies\Filters\DateModelApply;
use MoonShine\Laravel\Applies\Filters\DateRangeModelApply;
use MoonShine\Laravel\Applies\Filters\JsonModelApply;
use MoonShine\Laravel\Applies\Filters\MorphToModelApply;
use MoonShine\Laravel\Applies\Filters\RangeModelApply;
use MoonShine\Laravel\Applies\Filters\SelectModelApply;
use MoonShine\Laravel\Applies\Filters\TextModelApply;
use MoonShine\Laravel\Collections\Fields;
use MoonShine\Laravel\Commands\InstallCommand;
use MoonShine\Laravel\Commands\MakeApplyCommand;
use MoonShine\Laravel\Commands\MakeComponentCommand;
use MoonShine\Laravel\Commands\MakeControllerCommand;
use MoonShine\Laravel\Commands\MakeFieldCommand;
use MoonShine\Laravel\Commands\MakeHandlerCommand;
use MoonShine\Laravel\Commands\MakeLayoutCommand;
use MoonShine\Laravel\Commands\MakePageCommand;
use MoonShine\Laravel\Commands\MakePolicyCommand;
use MoonShine\Laravel\Commands\MakeResourceCommand;
use MoonShine\Laravel\Commands\MakeTypeCastCommand;
use MoonShine\Laravel\Commands\MakeUserCommand;
use MoonShine\Laravel\Commands\OptimizeClearCommand;
use MoonShine\Laravel\Commands\OptimizeCommand;
use MoonShine\Laravel\Commands\PublishCommand;
use MoonShine\Laravel\DependencyInjection\AssetResolver;
use MoonShine\Laravel\DependencyInjection\MoonShine;
use MoonShine\Laravel\DependencyInjection\MoonShineConfigurator;
use MoonShine\Laravel\DependencyInjection\MoonShineRouter;
use MoonShine\Laravel\DependencyInjection\Request;
use MoonShine\Laravel\DependencyInjection\Translator;
use MoonShine\Laravel\DependencyInjection\ViewRenderer;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Fields\Relationships\BelongsToMany;
use MoonShine\Laravel\Fields\Relationships\MorphTo;
use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Laravel\MoonShineRequest;
use MoonShine\Laravel\Notifications\MoonShineNotification;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Laravel\Storage\LaravelStorage;
use MoonShine\MenuManager\MenuAutoloader;
use MoonShine\MenuManager\MenuManager;
use MoonShine\UI\Applies\AppliesRegister;
use MoonShine\UI\Fields\Checkbox;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\DateRange;
use MoonShine\UI\Fields\File;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Range;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use Psr\SimpleCache\CacheInterface;

final class MoonShineServiceProvider extends ServiceProvider
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
        OptimizeCommand::class,
        OptimizeClearCommand::class,
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
            'model' => Config::get('moonshine.auth.model', MoonshineUser::class),
        ]);

        return $this;
    }

    /**
     * Register the route middleware.
     */
    protected function registerRouteMiddleware(): self
    {
        app('router')->middlewareGroup('moonshine', [
            ...moonshineConfig()->getMiddleware(),
        ]);

        return $this;
    }

    protected function registerBindings(): self
    {
        $this->app->singleton(CoreContract::class, MoonShine::class);

        Core::setInstance(static fn () => app(CoreContract::class));

        $this->app->bind(RouterContract::class, MoonShineRouter::class);

        $this->app->{app()->runningUnitTests() ? 'bind' : 'singleton'}(
            CrudRequestContract::class,
            static fn ($app): MoonShineRequest => MoonShineRequest::createFrom($app['request'])
        );

        $this->app->singleton(MenuManagerContract::class, MenuManager::class);
        $this->app->singleton(AssetManagerContract::class, AssetManager::class);
        $this->app->singleton(AssetResolverContract::class, AssetResolver::class);
        $this->app->{app()->runningUnitTests() ? 'bind' : 'singleton'}(ConfiguratorContract::class, MoonShineConfigurator::class);
        $this->app->singleton(AppliesRegisterContract::class, AppliesRegister::class);
        $this->app->singleton(MenuAutoloaderContract::class, MenuAutoloader::class);
        $this->app->singleton(CacheAttributesContract::class, CacheAttributes::class);
        $this->app->singleton(
            MoonShineNotificationContract::class,
            moonshineConfig()->isUseDatabaseNotifications() ? MoonShineNotification::class : MemoryNotification::class
        );
        $this->app->singleton(OptimizerCollectionContract::class, fn (): OptimizerCollection => new OptimizerCollection(
            cachePath: $this->app->basePath('bootstrap/cache/moonshine.php'),
            config   : $this->app->make(ConfiguratorContract::class)
        ));

        $this->app->bind(TranslatorContract::class, Translator::class);
        $this->app->bind(FieldsContract::class, Fields::class);
        $this->app->bind(ViewRendererContract::class, ViewRenderer::class);

        $this->app->bind(RequestContract::class, Request::class);
        $this->app->bind(
            CacheInterface::class,
            static fn (Application $app): CacheInterface => $app->get('cache')->store(
                $app->get(ConfiguratorContract::class)->getCacheDriver()
            )
        );

        $this->app->bind(StorageContract::class, static fn (Application $app, array $parameters): LaravelStorage => new LaravelStorage(
            $parameters['disk'] ?? $parameters[0] ?? 'public',
            $app->get('filesystem')
        ));

        $this->app->scoped(ColorManagerContract::class, ColorManager::class);

        $this->app->bind(IndexPageContract::class, IndexPage::class);
        $this->app->bind(FormPageContract::class, FormPage::class);
        $this->app->bind(DetailPageContract::class, DetailPage::class);

        return $this;
    }

    protected function registerBladeDirectives(): self
    {
        $this->callAfterResolving('blade.compiler', static function (BladeCompiler $blade): void {
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

    protected function registerMacros(): self
    {
        \Illuminate\Http\Request::macro('getScalar', function (string $key, mixed $default = null): mixed {
            $value = request()->input($key, $default);
            $default = \is_scalar($default) ? $default : null;

            return \is_scalar($value) ? $value : $default;
        });

        Router::macro(
            'moonshine',
            fn (Closure $callback, bool $withResource = false, bool $withPage = false, bool $withAuthenticate = false) => $this->group(
                moonshineConfig()->getDefaultRouteGroup(),
                function () use ($callback, $withResource, $withPage, $withAuthenticate): void {
                    $parameters = [];

                    if ($withResource) {
                        $parameters['prefix'] = '{resourceUri}';
                    }

                    if ($withPage) {
                        $parameters['prefix'] = ($parameters['prefix'] ?? '') . '/{pageUri}';
                    }

                    Router::group(
                        $parameters,
                        fn () => $callback($this)
                    )->middleware($withAuthenticate ? moonshineConfig()->getAuthMiddleware() : null);
                }
            )
        );

        return $this;
    }

    protected function registerApplies(): self
    {
        $appliesRegister = $this->app->get(AppliesRegisterContract::class);

        $appliesRegister->defaultFor(ModelResource::class);

        $appliesRegister->for(ModelResource::class)->fields()->push([
            File::class => FileModelApply::class,
        ]);

        $appliesRegister->for(ModelResource::class)->filters()->push([
            Date::class => DateModelApply::class,
            Range::class => RangeModelApply::class,
            DateRange::class => DateRangeModelApply::class,
            BelongsToMany::class => BelongsToManyModelApply::class,
            MorphTo::class => MorphToModelApply::class,
            Json::class => JsonModelApply::class,
            Text::class => TextModelApply::class,
            Textarea::class => TextModelApply::class,
            Checkbox::class => CheckboxModelApply::class,
            Select::class => SelectModelApply::class,
            BelongsTo::class => BelongsToModelApply::class,
        ]);

        return $this;
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            MoonShine::path('/config/moonshine.php'),
            'moonshine'
        );

        $this
            ->registerBindings()
            ->registerMacros();
    }

    public function boot(): void
    {
        $this->publishes([
            MoonShine::path('/config/moonshine.php') => config_path(
                'moonshine.php'
            ),
        ]);

        if (moonshineConfig()->isUseMigrations()) {
            $this->loadMigrationsFrom(MoonShine::path('/database/migrations'));

            $this->publishes([
                MoonShine::path('/database/migrations') => database_path('migrations'),
            ], 'moonshine-migrations');
        }

        $this->loadTranslationsFrom(MoonShine::path('/lang'), 'moonshine');
        $this->loadRoutesFrom(MoonShine::path('/routes/moonshine.php'));
        $this->loadViewsFrom(__DIR__ . '/../../../UI/resources/views', 'moonshine');

        $this->publishes([
            MoonShine::UIPath('/dist') => public_path('vendor/moonshine'),
        ], ['moonshine-assets', 'laravel-assets']);

        $this->publishes([
            MoonShine::path('/lang') => $this->app->langPath(
                'vendor/moonshine'
            ),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands($this->commands);

            if (method_exists($this, 'optimizes')) {
                $this->optimizes(
                    optimize: 'moonshine:optimize',
                    clear   : 'moonshine:optimize-clear',
                    key     : 'moonshine'
                );
            }
        }

        Blade::componentNamespace('MoonShine\UI\Components', 'moonshine');
        Blade::componentNamespace('MoonShine\Laravel\Components', 'moonshine-laravel');
        Blade::componentNamespace('MoonShine\Crud\Components', 'moonshine-crud');

        $this
            ->registerBladeDirectives()
            ->registerRouteMiddleware()
            ->registerAuth()
            ->registerApplies();

        // Octane events
        tap($this->app['events'], static function ($event): void {
            $event->listen(
                'Laravel\Octane\Events\RequestHandled',
                static fn () => moonshine()->flushState()
            );
        });
    }
}
