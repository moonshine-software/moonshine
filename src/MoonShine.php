<?php

declare(strict_types=1);

namespace MoonShine;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Menu\MenuElement;
use MoonShine\Pages\Page;
use MoonShine\Pages\Pages;
use MoonShine\Resources\MoonShineProfileResource;
use MoonShine\Resources\Resource;
use Throwable;

class MoonShine
{
    public const DIR = 'app/MoonShine';

    public const NAMESPACE = 'App\MoonShine';

    protected static ?Collection $resources = null;

    protected static ?Pages $pages = null;

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

    public static function getResourceFromUriKey(?string $uri): ?ResourceContract
    {
        if(is_null($uri)) {
            return null;
        }

        return self::getResources()
            ->first(
                fn (ResourceContract $resource): bool => $resource->uriKey() === $uri
            );
    }

    public static function getPageFromUriKey(?string $uri): ?Page
    {
        if(is_null($uri)) {
            return null;
        }

        return self::getPages()->findByUri($uri);
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

    public static function addResource(Resource $resource): void
    {
        if(is_null(self::$resources)) {
            self::$resources = collect([$resource]);

            return;
        }

        self::$resources->add($resource);
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
     * Register pages in the system
     *
     * @param  array<Page>  $data
     */
    public static function pages(array $data): void
    {
        self::$pages = Pages::make($data);
    }

    /**
     * Get collection of registered pages
     *
     * @return Pages<Page>
     */
    public static function getPages(): Pages
    {
        return self::$pages ?? Pages::make();
    }

    /**
     * Get collection of registered menu
     *
     * @return Collection<MenuElement>
     */
    public static function getMenu(): Collection
    {
        return self::$menu ?? collect();
    }

    /**
     * Register Menu with resources and pages in the system
     *
     * @param  array<MenuElement>  $data
     * @throws Throwable
     */
    public static function menu(array $data): void
    {
        self::$menu = self::getMenu();
        self::$pages = self::getPages();
        self::$resources = self::getResources();

        collect($data)->each(
            function (MenuElement $item): void {
                self::$menu->add($item);
                self::resolveMenuItem($item);
            }
        );

        self::$resources->add(new MoonShineProfileResource());
        self::$pages->add(new (config('moonshine.pages.dashboard'))());
    }

    private static function resolveMenuItem(MenuElement $element): void
    {
        if ($element->isGroup()) {
            $element->items()->each(
                fn (MenuElement $item) => self::resolveMenuItem($item)
            );
        } elseif($element->isItem()) {
            $filler = $element->getFiller();

            if ($filler instanceof Page) {
                self::$pages->add($filler);
            }

            if ($filler instanceof ResourceContract) {
                self::$resources->add($filler);
            }
        }
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
