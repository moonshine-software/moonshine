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
use Laravel\Octane\Events\RequestHandled;
use MoonShine\AssetManager\AssetManager;
use MoonShine\ColorManager\ColorManager;
use MoonShine\Contracts\Collections\FieldsCollection;
use MoonShine\Core\MoonShineConfigurator;
use MoonShine\Core\Request;
use MoonShine\Core\MoonShineRouter;
use MoonShine\Core\Storage\StorageContract;
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
use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Laravel\MoonShineRequest;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Laravel\Storage\LaravelStorage;
use MoonShine\MenuManager\MenuManager;
use MoonShine\MoonShine;
use MoonShine\Support\Enums\Env;
use MoonShine\UI\Applies\AppliesRegister;
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
            fn ($app): MoonShineRequest => MoonShineRequest::createFrom($app['request'])
        );

        $this->app->singleton(MenuManager::class);
        $this->app->singleton(AssetManager::class);
        $this->app->singleton(AppliesRegister::class);
        $this->app->singleton(MoonShineConfigurator::class);

        $this->app->bind(MoonShineRouter::class);
        $this->app->bind(FieldsCollection::class, Fields::class);
        $this->app->bind(StorageContract::class, fn(Application $app, array $parameters) =>  new LaravelStorage(
            $parameters['disk'] ?? $parameters[0] ?? 'public',
            $app->get('filesystem')
        ));

        $this->app->scoped(ColorManager::class);

        MoonShine::flushStates(static function () {
            moonshineCache()->flush();
        });

        MoonShine::setEnv(
            Env::fromString(app()->environment())
        );

        MoonShine::renderUsing(static function (string $view, array $data) {
            return view($view, $data);
        });

        MoonShine::containerUsing(static function (string $id, mixed $default = null, ...$parameters): mixed {
            if(!is_null($default) && !app()->has($id)) {
                return $default;
            }

            return app($id, ...$parameters);
        });

        FormElement::resolveErrors(
            static fn(?string $bag) => app('session')
                ->get('errors')
                ?->{$bag}
                ?->toArray() ?? []
        );

        MoonShine::requestUsing(static fn() => new Request(
            request: app(ServerRequestInterface::class),
            session: fn (string $key, mixed $default) => session($key, $default),
            file: fn (string $key) => request()->file($key, request()->get($key, false)),
            old: fn (string $key, mixed $default) => session()->getOldInput($key, $default)
        ));

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

        tap($this->app['events'], function ($event): void {
            $event->listen(RequestHandled::class, function (RequestHandled $event): void {
                moonshine()->flushState();
            });
        });
    }
}
