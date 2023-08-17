<?php

declare(strict_types=1);

namespace MoonShine;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use MoonShine\Contracts\Menu\MenuElement;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Menu\MenuGroup;
use MoonShine\Menu\MenuItem;
use MoonShine\Menu\MenuSection;
use MoonShine\Resources\MoonShineProfileResource;

class MoonShine
{
    public const DIR = 'app/MoonShine';

    public const NAMESPACE = 'App\MoonShine';

    protected static ?Collection $resources = null;

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

    public static function getResourceFromUriKey(string $uri): ?ResourceContract
    {
        return self::getResources()
            ->first(
                fn (ResourceContract $resource): bool => $resource->uriKey() === $uri
            );
    }

    /**
     * Register resources in the system
     *
     * @param  array<ResourceContract>  $data
     */
    public static function resources(array $data): void
    {
        self::$resources = collect($data);
    }

    /**
     * Get collection of registered resources
     *
     * @return Collection<ResourceContract>
     */
    public static function getResources(): Collection
    {
        return self::$resources ?? collect();
    }

    /**
     * Get collection of registered menu
     *
     * @return Collection<MenuSection>
     */
    public static function getMenu(): Collection
    {
        return self::$menu ?? collect();
    }

    /**
     * Register Menu with resources and pages in the system
     *
     * @param  array<string|MenuSection|ResourceContract>  $data
     */
    public static function menu(array $data): void
    {
        self::$resources = self::getResources();
        self::$menu = self::getMenu();

        self::$resources->add(new MoonShineProfileResource());

        collect($data)->each(function ($item): void {
            $item = is_string($item) ? new $item() : $item;

            if ($item instanceof ResourceContract) {
                self::$resources->add($item);
                self::$menu->add(new MenuItem($item->title(), $item));
            } elseif ($item instanceof MenuElement) {
                self::$resources->when(
                    $item->resource(),
                    fn ($r): Collection => $r->add($item->resource())
                );
                self::$menu->add($item);
            } elseif ($item instanceof MenuGroup) {
                self::$menu->add($item);

                $item->items()->each(function ($subItem): void {
                    self::$resources->when(
                        $subItem->resource(),
                        fn ($r): Collection => $r->add($subItem->resource())
                    );
                });
            }
        });
    }

    /**
     * Register moonshine routes and resources routes in the system
     */
    public static function resolveRoutes(): void
    {
        Route::prefix(config('moonshine.route.prefix', ''))
            ->middleware('moonshine')
            ->as('moonshine.')->group(function (): void {
                self::getResources()->each(
                    static function (ResourceContract $resource): void {
                        $resource->routes();
                    }
                );
            });
    }
}
