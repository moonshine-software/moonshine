<?php

declare(strict_types=1);

namespace MoonShine\Laravel;

use Closure;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use MoonShine\Core\Contracts\ConfiguratorContract;
use MoonShine\Laravel\Pages\Page;
use MoonShine\Laravel\Exceptions\MoonShineNotFoundException;
use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Contracts\Forms\FormContract;
use MoonShine\UI\MoonShineLayout;
use Throwable;

final class MoonShineConfigurator implements ConfiguratorContract
{
    private array $items;

    private readonly Collection $authorizationRules;

    public function __construct(Repository $repository)
    {
        $this->items = $repository->get('moonshine', []);
        $this->authorizationRules = Collection::make();
        $this
            ->set('dir', 'app/MoonShine')
            ->set('namespace', 'App\MoonShine');
    }

    public function dir(string $dir, string $namespace): self
    {
        return $this
            ->set('dir', $dir)
            ->set('namespace', $namespace);
    }

    public function getDir(string $path = ''): string
    {
        return $this->get('dir') . $path;
    }

    public function getNamespace(string $path = ''): string
    {
        return $this->get('namespace') . $path;
    }

    /**
     * @return list<class-string>
     */
    public function getMiddlewares(): array
    {
        return $this->get('middlewares', []);
    }

    /**
     * @param  list<class-string>|Closure  $middlewares
     */
    public function middlewares(array|Closure $middlewares): self
    {
        return $this->set('middlewares', $middlewares);
    }

    public function exceptMiddlewares(array|string $except = []): self
    {
        $except = is_string($except) ? [$except] : $except;

        $middlewares = collect($this->getMiddlewares())
            ->filter(fn ($class): bool => ! in_array($class, $except, true))
            ->toArray();

        return $this->middlewares($middlewares);
    }

    public function getTitle(): string
    {
        return $this->get('title', '');
    }

    public function title(string|Closure $title): self
    {
        return $this->set('title', $title);
    }

    /**
     * @return string[]
     */
    public function getLocales(): array
    {
        return $this->get('locales', []);
    }

    /**
     * @param  string[]|Closure  $locales
     */
    public function locales(array|Closure $locales): self
    {
        return $this->set('locales', $locales);
    }

    public function locale(string $locale): self
    {
        return $this->set('locale', $locale);
    }

    public function getLocale(): string
    {
        return $this->get('locale', 'en');
    }

    /**
     * @return array<string, string>
     */
    public function getSocialite(): array
    {
        return $this->get('socialite', []);
    }

    /**
     * @param  array<string, string>|Closure  $socialite
     */
    public function socialite(array|Closure $socialite): self
    {
        return $this->set('socialite', $socialite);
    }

    public function getCacheDriver(): string
    {
        return $this->get('cache', 'file');
    }

    public function cacheDriver(string|Closure $driver): self
    {
        return $this->set('cache', $driver);
    }

    public function getDisk(): string
    {
        return $this->get('disk', 'public');
    }

    /**
     * @param  string[]|Closure  $options
     */
    public function disk(string|Closure $disk, array|Closure $options = []): self
    {
        return $this
            ->set('disk', $disk)
            ->set('disk_options', $options);
    }

    /**
     * @return string[]
     */
    public function getDiskOptions(): array
    {
        return $this->get('disk_options', []);
    }

    /**
     * @return list<class-string>
     */
    public function getGlobalSearch(): array
    {
        return $this->get('global_search', []);
    }

    /**
     * @param  list<class-string>|Closure  $models
     */
    public function globalSearch(array|Closure $models = []): self
    {
        return $this->set('global_search', $models);
    }

    public function isUseMigrations(): bool
    {
        return $this->get('use_migrations', true);
    }

    public function useMigrations(): self
    {
        return $this->set('use_migrations', true);
    }

    public function isUseNotifications(): bool
    {
        return $this->get('use_notifications', true);
    }

    public function useNotifications(): self
    {
        return $this->set('use_notifications', true);
    }

    /**
     * @return class-string<Throwable>
     */
    public function getNotFoundException(): string
    {
        return $this->get(
            'not_found_exception',
            MoonShineNotFoundException::class
        );
    }

    /**
     * @param  class-string<Throwable>|Closure  $exception
     */
    public function notFoundException(string|Closure $exception): self
    {
        return $this->set('not_found_exception', $exception);
    }

    public function guard(string|Closure $guard): self
    {
        return $this->set('auth', [
            'guard' => $guard,
        ]);
    }

    public function getGuard(): string
    {
        return $this->get('auth.guard', 'moonshine');
    }

    public function getUserField(string $field, string $default = null): string
    {
        return $this->get("user_fields.$field", $default ?? $field);
    }

    public function userField(string $field, string|Closure $value): self
    {
        return $this->set("user_fields.$field", $value);
    }

    public function isAuthEnabled(): bool
    {
        return $this->get('auth.enabled', true);
    }

    public function authDisable(): self
    {
        return $this->set('auth.enabled', false);
    }

    /**
     * @return  list<class-string>
     */
    public function getAuthPipelines(): array
    {
        return $this->get('auth.pipelines', []);
    }

    /**
     * @param  list<class-string>|Closure  $pipelines
     */
    public function authPipelines(array|Closure $pipelines): self
    {
        return $this->set('auth.pipelines', $pipelines);
    }

    /**
     * @return class-string
     */
    public function getAuthMiddleware(): string
    {
        return $this->get('auth.middleware', '');
    }

    /**
     * @param  class-string|Closure  $middleware
     */
    public function authMiddleware(string|Closure $middleware): self
    {
        return $this->set('auth.middleware', $middleware);
    }

    public function isDefaultWithExport(): bool
    {
        return $this->get('default_with_export', true);
    }

    public function isDefaultWithImport(): bool
    {
        return $this->get('default_with_import', true);
    }

    public function defaultWithoutExport(): self
    {
        return $this->set('default_with_export', false);
    }

    public function defaultWithoutImport(): self
    {
        return $this->set('default_with_import', false);
    }

    public function getPagePrefix(): string
    {
        return $this->get('page_prefix', 'page');
    }

    public function prefixes(string|Closure $route, string|Closure $page): self
    {
        return $this
            ->set('prefix', $route)
            ->set('page_prefix', $page);
    }

    public function domain(string|Closure $domain): self
    {
        return $this->set('domain', $domain);
    }

    /**
     * @return array<string, string>
     */
    public function getDefaultRouteGroup(): array
    {
        return array_filter([
            'domain' => $this->get('domain', ''),
            'prefix' => $this->get('prefix', ''),
            'middleware' => 'moonshine',
            'as' => 'moonshine.',
        ]);
    }

    /**
     * @return class-string<MoonShineLayout>
     */
    public function getLayout(): string
    {
        return $this->get('layout', AppLayout::class);
    }

    /**
     * @param  class-string<MoonShineLayout>|Closure  $layout
     */
    public function layout(string|Closure $layout): self
    {
        return $this->set('layout', $layout);
    }

    public function getHomeRoute(): string
    {
        return $this->get('home_route', 'moonshine.index');
    }

    public function homeRoute(string|Closure $route): self
    {
        return $this->set('home_route', $route);
    }

    public function getAuthorizationRules(): Collection
    {
        return $this->authorizationRules;
    }

    public function authorizationRules(Closure $rule): self
    {
        $this->authorizationRules->add($rule);

        return $this;
    }

    /**
     * @template-covariant T of Page
     * @param  class-string<T>  $default
     */
    public function getPage(string $name, string $default, mixed ...$parameters): Page
    {
        $class = $this->get("pages.$name", $default);

        return moonshine()->getContainer($class, null, ...$parameters);
    }

    /**
     * @return list<class-string<Page>>
     */
    public function getPages(): array
    {
        return $this->get('pages', []);
    }

    /**
     * @param  class-string<Page>  $old
     * @param  class-string<Page>  $new
     * @return $this
     */
    public function changePage(string $old, string $new): self
    {
        $pages = $this->getPages();

        return $this->set(
            'pages',
            collect($pages)
                ->map(fn (string $page): string => $page === $old ? $new : $page)
                ->toArray()
        );
    }

    /**
     * @template-covariant T of FormContract
     * @param  class-string<T>  $default
     */
    public function getForm(string $name, string $default, mixed ...$parameters): FormBuilder
    {
        $class = $this->get("forms.$name", $default);

        return call_user_func(
            new $class(...$parameters)
        );
    }

    public function has(string $key): bool
    {
        return Arr::has($this->items, $key);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return value(
            Arr::get($this->items, $key, $default)
        );
    }

    public function set(string $key, mixed $value): self
    {
        $this->items[$key] = $value;

        return $this;
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->has($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->set($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->set($offset, null);
    }
}
