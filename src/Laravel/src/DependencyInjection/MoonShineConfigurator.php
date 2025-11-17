<?php

declare(strict_types=1);

namespace MoonShine\Laravel\DependencyInjection;

use Closure;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use MoonShine\ColorManager\Palettes\PurplePalette;
use MoonShine\Contracts\ColorManager\PaletteContract;
use MoonShine\Contracts\Core\DependencyInjection\ConfiguratorContract;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\Crud\Layouts\AbstractLayout;
use MoonShine\Laravel\Exceptions\MoonShineNotFoundException;
use MoonShine\Laravel\Http\Middleware\ChangeLocale;
use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\Support\Enums\Ability;
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
            ->set('dir', $this->items['dir'] ?? 'app/MoonShine')
            ->set('namespace', $this->items['namespace'] ?? 'App\MoonShine');
    }

    public function dir(string $dir, string $namespace): self
    {
        return $this
            ->set('dir', $dir)
            ->set('namespace', $namespace);
    }

    public function getDir(string $path = '', ?string $base = null): string
    {
        $base ??= $this->get('dir');

        return $base . '/' . trim($path, '/');
    }

    public function getNamespace(string $path = '', ?string $base = null): string
    {
        $base ??= $this->get('namespace');

        $path = str_replace('/', '\\', $path);

        return $base . '\\' . trim($path, '\\');
    }

    /**
     * @return list<class-string>
     */
    public function getMiddleware(): array
    {
        return $this->get('middleware', []);
    }

    public function getTitle(): string
    {
        return $this->get('title', '');
    }

    public function title(string|Closure $title): self
    {
        return $this->set('title', $title);
    }

    public function getLogo(bool $small = false): ?string
    {
        return $this->get($small ? 'logo_small' : 'logo');
    }

    public function logo(string|Closure $logo, bool $small = false): self
    {
        return $this->set($small ? 'logo_small' : 'logo', $logo);
    }

    /**
     * @return string[]
     */
    public function getLocales(): array
    {
        return Collection::make($this->get('locales', []))
            ->mapWithKeys(fn ($value, $key): array => [is_numeric($key) ? $value : $key => $value])
            ->toArray();
    }

    /**
     * @param  string[]|Closure  $locales
     */
    public function locales(array|Closure $locales): self
    {
        return $this->set('locales', $locales);
    }

    public function addLocales(array|string $locales): self
    {
        if (\is_string($locales)) {
            $locales = [$locales];
        }

        return $this->set('locales', [
            ...$this->getLocales(),
            ...$locales,
        ]);
    }

    public function locale(string $locale): self
    {
        return $this->set('locale', $locale);
    }

    public function getLocale(): string
    {
        return $this->get('locale', 'en');
    }

    public function localeKey(string $name): self
    {
        return $this->set('locale_key', $name);
    }

    public function getLocaleKey(): string
    {
        return $this->get('locale_key', ChangeLocale::KEY);
    }

    public function getCacheDriver(): string
    {
        return $this->get('cache', 'file');
    }

    public function cacheDriver(string|Closure $driver): self
    {
        return $this->set('cache', $driver);
    }

    /**
     * @return string
     */
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

    public function getUserAvatarsDir(): string
    {
        return $this->get('user_avatars_dir', 'moonshine_users');
    }

    public function userAvatarsDir(string|Closure $dir): self
    {
        return $this->set('user_avatars_dir', $dir);
    }

    public function isUseMigrations(): bool
    {
        return $this->get('use_migrations', true);
    }

    public function useMigrations(): self
    {
        return $this->set('use_migrations', true);
    }

    public function isUseProfile(): bool
    {
        return $this->get('use_profile', true);
    }

    public function isUseRoutes(): bool
    {
        return $this->get('use_routes', true);
    }

    public function isUseNotifications(): bool
    {
        return $this->get('use_notifications', false);
    }

    public function useNotifications(): self
    {
        return $this->set('use_notifications', true);
    }

    public function isUseDatabaseNotifications(): bool
    {
        return $this->get('use_database_notifications', false);
    }

    public function useDatabaseNotifications(): self
    {
        return $this->set('use_database_notifications', true);
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

    public function getUserField(string $field, ?string $default = null): string|false
    {
        return $this->get("user_fields.$field", $default ?? $field);
    }

    public function userField(string $field, string|false|Closure $value): self
    {
        return $this->set("user_fields.$field", $value);
    }

    public function isAuthEnabled(): bool
    {
        return $this->get('auth.enabled', true);
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
     * @return list<class-string>
     */
    public function getAuthMiddleware(): array
    {
        $middleware = $this->get('auth.middleware', []);

        return \is_string($middleware) ? [$middleware] : $middleware;
    }

    public function getPagePrefix(): string
    {
        return $this->get('page_prefix', 'page');
    }

    public function getResourcePrefix(): string
    {
        return $this->get('resource_prefix', 'resource');
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
     * @return class-string<AbstractLayout>
     */
    public function getLayout(): string
    {
        return $this->get('layout', AppLayout::class);
    }

    /**
     * @return class-string<PaletteContract>
     */
    public function getPalette(): string
    {
        return $this->get('palette', PurplePalette::class);
    }

    /**
     * @param  class-string<AbstractLayout>|Closure  $layout
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

    public function getHomeUrl(): ?string
    {
        return $this->get('home_url');
    }

    public function homeUrl(string|Closure $route): self
    {
        return $this->set('home_url', $route);
    }

    public function getAuthorizationRules(): Collection
    {
        return $this->authorizationRules;
    }

    /**
     * @param  Closure(ResourceContract $ctx, mixed $user, Ability $ability, mixed $data): bool  $rule
     */
    public function authorizationRules(Closure $rule): self
    {
        $this->authorizationRules->add($rule);

        return $this;
    }

    public function getPage(string $name, string $default, mixed ...$parameters): PageContract
    {
        $class = $this->get("pages.$name", $default);

        return moonshine()->getContainer($class, null, ...$parameters);
    }

    /**
     * @return list<class-string<PageContract>>
     */
    public function getPages(): array
    {
        return $this->get('pages', []);
    }

    /**
     * @param  class-string<PageContract>  $old
     * @param  class-string<PageContract>  $new
     */
    public function changePage(string $old, string $new): self
    {
        $pages = $this->getPages();

        return $this->set(
            'pages',
            Collection::make($pages)
                ->map(static fn (string $page): string => $page === $old ? $new : $page)
                ->toArray()
        );
    }

    public function getForm(string $name, string $default, mixed ...$parameters): FormBuilderContract
    {
        $class = $this->get("forms.$name", $default);

        return \call_user_func(
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
