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
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class MoonShineConfigurator implements ConfiguratorContract
{
    /**
     * @var array<string, mixed>
     */
    private array $items;

    /**
     * @var Collection<int, Closure(ResourceContract, mixed, Ability, mixed): bool>
     */
    private readonly Collection $authorizationRules;

    /** @var null|(Closure((Closure(): Response)): Response) */
    private ?Closure $logoutUsing = null;

    /** @var null|(Closure((Closure(): Response)): Response) */
    private ?Closure $authenticateUsing = null;

    public function __construct(Repository $repository)
    {
        /** @var array<string, mixed> $items */
        $items = $repository->get('moonshine', []);
        $this->items = $items;
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
        /** @var string $base */
        $base ??= $this->get('dir');

        return $base . '/' . trim($path, '/');
    }

    public function getNamespace(string $path = '', ?string $base = null): string
    {
        /** @var string $base */
        $base ??= $this->get('namespace');

        $path = str_replace('/', '\\', $path);

        return $base . '\\' . trim($path, '\\');
    }

    /**
     * @return list<string>
     */
    public function getMiddleware(): array
    {
        return array_values($this->stringArrayValue($this->get('middleware', [])));
    }

    public function getTitle(): string
    {
        return $this->stringValue($this->get('title', ''));
    }

    public function title(string|Closure $title): self
    {
        return $this->set('title', $title);
    }

    public function getLogo(bool $small = false): ?string
    {
        $value = $this->get($small ? 'logo_small' : 'logo');

        return $value === null ? null : $this->stringValue($value);
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
        /** @var array<array-key, string> $locales */
        $locales = $this->get('locales', []);

        return Collection::make($locales)
            ->mapWithKeys(fn ($value, $key): array => [is_numeric($key) ? $value : $key => $value])
            ->all();
    }

    /**
     * @param  string[]|Closure  $locales
     */
    public function locales(array|Closure $locales): self
    {
        return $this->set('locales', $locales);
    }

    /**
     * @param array<string>|string $locales
     */
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
        return $this->stringValue($this->get('locale', 'en'));
    }

    public function localeKey(string $name): self
    {
        return $this->set('locale_key', $name);
    }

    public function getLocaleKey(): string
    {
        return $this->stringValue($this->get('locale_key', ChangeLocale::KEY));
    }

    public function getCacheDriver(): string
    {
        return $this->stringValue($this->get('cache', 'file'));
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
        return $this->stringValue($this->get('disk', 'public'));
    }

    /**
     * @param  array<string, mixed>|Closure  $options
     */
    public function disk(string|Closure $disk, array|Closure $options = []): self
    {
        return $this
            ->set('disk', $disk)
            ->set('disk_options', $options);
    }

    /**
     * @return array<string, mixed>
     */
    public function getDiskOptions(): array
    {
        $options = $this->get('disk_options', []);

        if (! \is_array($options)) {
            throw new \TypeError('Expected an array of disk options, got ' . get_debug_type($options));
        }

        $result = [];

        foreach ($options as $key => $value) {
            if (! \is_string($key)) {
                throw new \TypeError('Disk option names must be strings.');
            }

            $result[$key] = $value;
        }

        return $result;
    }

    public function getUserAvatarsDir(): string
    {
        return $this->stringValue($this->get('user_avatars_dir', 'moonshine_users'));
    }

    public function userAvatarsDir(string|Closure $dir): self
    {
        return $this->set('user_avatars_dir', $dir);
    }

    public function isUseMigrations(): bool
    {
        return $this->boolValue($this->get('use_migrations', true));
    }

    public function useMigrations(): self
    {
        return $this->set('use_migrations', true);
    }

    public function isUseProfile(): bool
    {
        return $this->boolValue($this->get('use_profile', true));
    }

    public function isUseRoutes(): bool
    {
        return $this->boolValue($this->get('use_routes', true));
    }

    public function isUseNotifications(): bool
    {
        return $this->boolValue($this->get('use_notifications', false));
    }

    public function useNotifications(): self
    {
        return $this->set('use_notifications', true);
    }

    public function isUseDatabaseNotifications(): bool
    {
        return $this->boolValue($this->get('use_database_notifications', false));
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
        return $this->classStringValue(
            $this->get('not_found_exception', MoonShineNotFoundException::class),
            Throwable::class,
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
        return $this->stringValue($this->get('auth.guard', 'moonshine'));
    }

    public function getUserField(string $field, ?string $default = null): string|false
    {
        $value = $this->get("user_fields.$field", $default ?? $field);

        return $value === false ? false : $this->stringValue($value);
    }

    public function userField(string $field, string|false|Closure $value): self
    {
        return $this->set("user_fields.$field", $value);
    }

    public function isAuthEnabled(): bool
    {
        return $this->boolValue($this->get('auth.enabled', true));
    }

    /**
     * @return  list<string>
     */
    public function getAuthPipelines(): array
    {
        return array_values($this->stringArrayValue($this->get('auth.pipelines', [])));
    }

    /**
     * @param  list<class-string>|Closure  $pipelines
     */
    public function authPipelines(array|Closure $pipelines): self
    {
        return $this->set('auth.pipelines', $pipelines);
    }

    /**
     * @return list<string>
     */
    public function getAuthMiddleware(): array
    {
        $middleware = $this->get('auth.middleware', []);

        return \is_string($middleware) ? [$middleware] : array_values($this->stringArrayValue($middleware));
    }

    public function getPagePrefix(): string
    {
        return $this->stringValue($this->get('page_prefix', 'page'));
    }

    public function getResourcePrefix(): string
    {
        return $this->stringValue($this->get('resource_prefix', 'resource'));
    }

    /**
     * @return array<string, string>
     */
    public function getDefaultRouteGroup(): array
    {
        /** @var array<string, string> */
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
        return $this->classStringValue($this->get('layout', AppLayout::class), AbstractLayout::class);
    }

    /**
     * @return class-string<PaletteContract>
     */
    public function getPalette(): string
    {
        return $this->classStringValue($this->get('palette', PurplePalette::class), PaletteContract::class);
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
        return $this->stringValue($this->get('home_route', 'moonshine.index'));
    }

    public function homeRoute(string|Closure $route): self
    {
        return $this->set('home_route', $route);
    }

    public function getHomeUrl(): ?string
    {
        $value = $this->get('home_url');

        return $value === null ? null : $this->stringValue($value);
    }

    public function homeUrl(string|Closure $route): self
    {
        return $this->set('home_url', $route);
    }

    /**
     * @return Collection<int, Closure(ResourceContract, mixed, Ability, mixed): bool>
     */
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

    /**
     * @template T of PageContract
     * @param class-string<T> $default
     * @return T
     */
    public function getPage(string $name, string $default, mixed ...$parameters): PageContract
    {
        /** @var class-string<T> $class */
        $class = $this->get("pages.$name", $default);

        return moonshine()->getContainer($class, null, ...$parameters);
    }

    /**
     * @return array<array-key, class-string<PageContract>>
     */
    public function getPages(): array
    {
        return array_map(
            fn (string $class) => $this->classStringValue($class, PageContract::class),
            $this->stringArrayValue($this->get('pages', [])),
        );
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
        /** @var class-string<\MoonShine\Contracts\UI\FormContract> $class */
        $class = $this->get("forms.$name", $default);

        return \call_user_func(
            new $class(...$parameters)
        );
    }

    /**
     * @param  Closure((Closure(): Response) $default): Response  $callback
     */
    public function authenticateUsing(Closure $callback): self
    {
        $this->authenticateUsing = $callback;

        return $this;
    }

    /**
     * @param  Closure(): Response  $default
     */
    public function handleAuthenticate(Closure $default): Response
    {
        if ($this->authenticateUsing instanceof Closure) {
            return \call_user_func($this->authenticateUsing, $default);
        }

        return $default();
    }

    /**
     * @param  Closure((Closure(): Response) $default): Response  $callback
     */
    public function logoutUsing(Closure $callback): self
    {
        $this->logoutUsing = $callback;

        return $this;
    }

    /**
     * @param  Closure(): Response  $default
     */
    public function handleLogout(Closure $default): Response
    {
        if ($this->logoutUsing instanceof Closure) {
            return \call_user_func($this->logoutUsing, $default);
        }

        return $default();
    }

    /** @return array<array-key, string> */
    private function stringArrayValue(mixed $value): array
    {
        if (! \is_array($value)) {
            throw new \TypeError('Expected an array configuration value, got ' . get_debug_type($value));
        }

        return array_map($this->stringValue(...), $value);
    }

    /**
     * @template T of object
     * @param class-string<T> $base
     * @return class-string<T>
     */
    private function classStringValue(mixed $value, string $base): string
    {
        $class = $this->stringValue($value);

        if (! is_a($class, $base, true)) {
            throw new \TypeError("Configuration class $class must implement or extend $base.");
        }

        return $class;
    }

    private function stringValue(mixed $value): string
    {
        if (! \is_string($value)) {
            throw new \TypeError('Expected a string configuration value, got ' . get_debug_type($value));
        }

        return $value;
    }

    private function boolValue(mixed $value): bool
    {
        if (! \is_bool($value)) {
            throw new \TypeError('Expected a boolean configuration value, got ' . get_debug_type($value));
        }

        return $value;
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
        $this->set($offset ?? throw new \InvalidArgumentException('A configuration key is required.'), $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->set($offset, null);
    }
}
