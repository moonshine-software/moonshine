<?php

declare(strict_types=1);

namespace MoonShine;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use MoonShine\Contracts\Menu\MenuElement;
use MoonShine\Menu\Menu;
use MoonShine\Menu\MenuGroup;
use MoonShine\Menu\MenuItem;
use MoonShine\Menu\MenuSection;
use MoonShine\Resources\CustomPage;
use MoonShine\Resources\Resource;

class MoonShine
{
    public const DIR = 'app/MoonShine';

    public const NAMESPACE = 'App\MoonShine';

    protected static ?Collection $resources = null;

    protected static ?Collection $pages = null;

    protected static ?Collection $menu = null;

    public static function path(string $path = ''): string
    {
        return realpath(
            dirname(__DIR__) . ($path !== '' && $path !== '0'
                ? DIRECTORY_SEPARATOR . $path : $path)
        );
    }

    public static function dir(string $path = ''): string
    {
        return (config('moonshine.dir') ?? static::DIR) . $path;
    }

    public static function namespace(string $path = ''): string
    {
        return (config('moonshine.namespace') ?? static::NAMESPACE) . $path;
    }

    public static function getResourceFromUriKey(string $uri): ?Resource
    {
        return self::getResources()
            ->first(
                fn (Resource $resource): bool => $resource->uriKey() === $uri
            );
    }

    /**
     * Get collection of registered resources
     *
     * @return Collection<Resource>
     */
    public static function getResources(): Collection
    {
        return self::$resources ?? collect();
    }

    /**
     * Register Menu with resources and pages in the system
     *
     * @param  array<string|MenuSection|Resource>  $data
     */
    public static function menu(array $data): void
    {
        self::$resources = self::getResources();
        self::$pages = self::getPages();
        self::$menu = collect();

        collect($data)->each(function ($item): void {
            $item = is_string($item) ? new $item() : $item;

            if ($item instanceof Resource) {
                self::$resources->add($item);
                self::$menu->add(new MenuItem($item->title(), $item));
            } elseif ($item instanceof CustomPage) {
                self::$pages->add($item);
                self::$menu->add(new MenuItem($item->label(), $item));
            } elseif ($item instanceof MenuElement) {
                self::$resources->when(
                    $item->resource(),
                    fn ($r): Collection => $r->add($item->resource())
                );
                self::$pages->when(
                    $item->page(),
                    fn ($r): Collection => $r->add($item->page())
                );
                self::$menu->add($item);
            } elseif ($item instanceof MenuGroup) {
                self::$menu->add($item);

                $item->items()->each(function ($subItem) use ($item): void {
                    self::$pages->when(
                        $subItem->page(),
                        function ($r) use ($subItem, $item): void {
                            $r->add(
                                $subItem->page()->breadcrumbs([
                                    $item->url() => $item->label(),
                                ])
                            );
                        }
                    );
                    self::$resources->when(
                        $subItem->resource(),
                        fn ($r): Collection => $r->add($subItem->resource())
                    );
                });
            }
        });

        self::$pages->add(
            CustomPage::make(
                __('moonshine::ui.profile'),
                'profile',
                'moonshine::profile'
            )
        );

        app(Menu::class)->register(self::$menu);

        //self::resolveResourcesRoutes();
    }

    /**
     * Get collection of registered pages
     *
     * @return Collection<CustomPage>
     */
    public static function getPages(): Collection
    {
        return self::$pages ?? collect();
    }

    /**
     * Register moonshine routes and resources routes in the system
     */
    protected static function resolveResourcesRoutes(): void
    {
        $middlewares = collect(config('moonshine.route.middleware'))
            ->reject(fn ($middleware): bool => $middleware === 'web')
            ->push('auth.moonshine')
            ->toArray();

        Route::prefix(config('moonshine.route.prefix', ''))
            ->middleware($middlewares)
            ->as('moonshine.')->group(function (): void {
                self::getResources()->each(
                    function (Resource $resource): void {
                        $resource->resolveRoutes();
                    }
                );
            });
    }

    /**
     * Register resources in the system
     *
     * @param  array<Resource>  $data
     */
    public static function resources(array $data): void
    {
        self::$resources = collect($data);
    }

    /**
     * Get collection of registered menu
     *
     * @return Collection<MenuSection>
     */
    public static function getMenu(): Collection
    {
        return self::$menu;
    }

    public static function isMoonShineRequest(): bool
    {
        $middlewares = request()?->route()?->gatherMiddleware() ?? [];

        return in_array('auth.moonshine', $middlewares);
    }
}
