<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Providers;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use Laravel\Octane\Events\RequestHandled;
use MoonShine\AssetManager\AssetManager;
use MoonShine\ColorManager\ColorManager;
use MoonShine\Core\Contracts\ConfiguratorContract;
use MoonShine\Core\Contracts\MoonShineEndpoints;
use MoonShine\Core\Contracts\StorageContract;
use MoonShine\Core\MoonShineRouter;
use MoonShine\Core\Request;
use MoonShine\Laravel\Applies\Fields\FileModelApply;
use MoonShine\Laravel\Applies\Filters\BelongsToManyModelApply;
use MoonShine\Laravel\Applies\Filters\CheckboxModelApply;
use MoonShine\Laravel\Applies\Filters\DateModelApply;
use MoonShine\Laravel\Applies\Filters\DateRangeModelApply;
use MoonShine\Laravel\Applies\Filters\MorphToModelApply;
use MoonShine\Laravel\Applies\Filters\RangeModelApply;
use MoonShine\Laravel\Applies\Filters\RepeaterModelApply;
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
use MoonShine\Laravel\Commands\PublishCommand;
use MoonShine\Laravel\Fields\Relationships\BelongsToMany;
use MoonShine\Laravel\Fields\Relationships\MorphTo;
use MoonShine\Laravel\LaravelEndpoints;
use MoonShine\Laravel\LaravelMoonShineRouter;
use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Laravel\MoonShineConfigurator;
use MoonShine\Laravel\MoonShineRequest;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Laravel\Resources\MoonShineUserRoleResource;
use MoonShine\Laravel\Storage\LaravelStorage;
use MoonShine\MenuManager\MenuItem;
use MoonShine\MenuManager\MenuManager;
use MoonShine\MoonShine;
use MoonShine\Support\Enums\Env;
use MoonShine\UI\Applies\AppliesRegister;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Contracts\Collections\FieldsCollection;
use MoonShine\UI\Fields\Checkbox;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\DateRange;
use MoonShine\UI\Fields\File;
use MoonShine\UI\Fields\FormElement;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Range;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use Psr\Http\Message\ServerRequestInterface;

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
            static fn ($app): MoonShineRequest => MoonShineRequest::createFrom($app['request'])
        );

        $this->app->singleton(MenuManager::class);
        $this->app->singleton(AssetManager::class);
        $this->app->singleton(AppliesRegister::class);
        $this->app->singleton(ConfiguratorContract::class, MoonShineConfigurator::class);

        $this->app->bind(MoonShineRouter::class, LaravelMoonShineRouter::class);
        $this->app->bind(MoonShineEndpoints::class, LaravelEndpoints::class);

        $this->app->bind(FieldsCollection::class, Fields::class);
        $this->app->bind(StorageContract::class, static fn (Application $app, array $parameters): LaravelStorage => new LaravelStorage(
            $parameters['disk'] ?? $parameters[0] ?? 'public',
            $app->get('filesystem')
        ));

        $this->app->scoped(ColorManager::class);

        MoonShine::flushStates(static function (): void {
            moonshineCache()->flush();
        });

        MoonShine::setEnv(
            Env::fromString(app()->environment())
        );

        MoonShine::renderUsing(static fn (string $view, array $data) => view($view, $data));

        MoonShine::containerResolver(static function (string $id, mixed $default = null, ...$parameters): mixed {
            if(! is_null($default) && ! app()->has($id)) {
                return $default;
            }

            return app($id, ...$parameters);
        });

        FormElement::errorsResolver(
            static fn (?string $bag) => app('session')
                ->get('errors')
                ?->{$bag}
                ?->toArray() ?? []
        );

        AssetManager::assetUsing(static fn (string $path): string => asset($path));
        AssetManager::viteDevResolver(static fn (string $path): string => Vite::useBuildDirectory('vendor/moonshine')
            ->useHotFile($path)
            ->withEntryPoints(['resources/css/main.css', 'resources/js/app.js'])
            ->toHtml());

        MoonShine::requestResolver(static fn (): Request => new Request(
            request: app(ServerRequestInterface::class),
            session: static fn (string $key, mixed $default) => session($key, $default),
            file: static fn (string $key) => request()->file($key, request()->input($key, false)),
            old: static fn (string $key, mixed $default) => session()->getOldInput($key, $default)
        ));

        MenuItem::macro('spa', function () {
            /** @var ModelResource $filler */
            $filler = value($this->getFiller());

            return $this->setUrl(
                fn() => $filler->getFragmentLoadUrl('_content')
            )->changeButton(
                static fn(ActionButton $btn) => $btn->async(selector: '#content')
            );
        });

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

    protected function registerApplies(): self
    {
        appliesRegister()->defaultFor(ModelResource::class);

        appliesRegister()->for(ModelResource::class)->fields()->push([
            File::class => FileModelApply::class,
        ]);

        appliesRegister()->for(ModelResource::class)->filters()->push([
            Date::class => DateModelApply::class,
            Range::class => RangeModelApply::class,
            DateRange::class => DateRangeModelApply::class,
            BelongsToMany::class => BelongsToManyModelApply::class,
            MorphTo::class => MorphToModelApply::class,
            Json::class => RepeaterModelApply::class,
            Text::class => TextModelApply::class,
            Textarea::class => TextModelApply::class,
            Checkbox::class => CheckboxModelApply::class,
            Select::class => SelectModelApply::class,
        ]);

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

            $this->publishesMigrations([
                MoonShine::path('/database/migrations') => database_path('migrations'),
            ]);
        }

        $this->publishes([
            MoonShine::path('/config/moonshine.php') => config_path(
                'moonshine.php'
            ),
        ]);

        $this->loadTranslationsFrom(MoonShine::path('/lang'), 'moonshine');
        $this->loadRoutesFrom(MoonShine::path('/routes/moonshine.php'));
        $this->loadViewsFrom(__DIR__ . '/../../../resources/views', 'moonshine');

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

        Blade::componentNamespace('MoonShine\UI\Components', 'moonshine');

        $this
            ->registerBladeDirectives()
            ->registerRouteMiddleware()
            ->registerAuth()
            ->registerApplies();

        // Octane events
        tap($this->app['events'], static function ($event): void {
            $event->listen(RequestHandled::class, static function (RequestHandled $event): void {
                moonshine()->flushState();
            });
        });
    }
}
