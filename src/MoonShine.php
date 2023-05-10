<?php

declare(strict_types=1);

namespace MoonShine;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use MoonShine\Contracts\Resources\ResourceContract;
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
            dirname(__DIR__).($path ? DIRECTORY_SEPARATOR.$path : $path)
        );
    }

    public static function dir(string $path = ''): string
    {
        return (config('moonshine.dir') ?? static::DIR).$path;
    }

    public static function namespace(string $path = ''): string
    {
        return (config('moonshine.namespace') ?? static::NAMESPACE).$path;
    }

    public static function getResourceFromUriKey(string $uri): ?ResourceContract
    {
        return self::getResources()
            ->first(fn (ResourceContract $resource) => $resource->uriKey() === $uri);
    }

    /**
     * @deprecated Will be deleted
     */
    public static function registerResources(array $data): void
    {
        self::menu($data);
    }

    /**
     * Register Menu with resources and pages in the system
     *
     * @param  array<string|MenuSection|ResourceContract>  $data
     * @return void
     */
    public static function menu(array $data): void
    {
        self::$resources = self::getResources();
        self::$pages = self::getPages();
        self::$menu = collect();

        collect($data)->each(function ($item) {
            $item = is_string($item) ? new $item() : $item;

            if ($item instanceof Resource) {
                self::$resources->add($item);
                self::$menu->add(new MenuItem($item->title(), $item));
            } elseif ($item instanceof CustomPage) {
                self::$pages->add($item);
                self::$menu->add(new MenuItem($item->label(), $item));
            } elseif ($item instanceof MenuItem) {
                self::$resources->when($item->resource(), fn ($r) => $r->add($item->resource()));
                self::$pages->when($item->page(), fn ($r) => $r->add($item->page()));
                self::$menu->add($item);
            } elseif ($item instanceof MenuGroup) {
                self::$menu->add($item);

                $item->items()->each(function ($subItem) {
                    self::$pages->when($subItem->page(), fn ($r) => $r->add($subItem->page()));
                    self::$resources->when($subItem->resource(), fn ($r) => $r->add($subItem->resource()));
                });
            }
        });

        self::$pages->add(
            CustomPage::make(__('moonshine::ui.profile'), 'profile', 'moonshine::profile')
        );

        app(Menu::class)->register(self::$menu);

        self::resolveResourcesRoutes();
    }

    /**
     * Register resources in the system
     *
     * @param  array<ResourceContract>  $data
     * @return void
     */
    public static function resources(array $data): void
    {
        self::$resources = collect($data);
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
     * Get collection of registered pages
     *
     * @return Collection<CustomPage>
     */
    public static function getPages(): Collection
    {
        return self::$pages ?? collect();
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

    /**
     * Register moonshine routes and resources routes in the system
     *
     * @return void
     */
    protected static function resolveResourcesRoutes(): void
    {
        Route::prefix(config('moonshine.route.prefix', ''))
            ->middleware(config('moonshine.route.middleware'))
            ->as('moonshine.')->group(function () {
                self::getResources()->each(function (ResourceContract $resource) {
                    $resource->resolveRoutes();
                });
            });
    }
}
