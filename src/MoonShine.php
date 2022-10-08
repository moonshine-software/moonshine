<?php

declare(strict_types=1);

namespace Leeto\MoonShine;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Stringable;
use Leeto\MoonShine\Contracts\Resources\ResourceContract;
use Leeto\MoonShine\Exceptions\MenuException;
use Leeto\MoonShine\Exceptions\MoonShineException;
use Leeto\MoonShine\Menu\Menu;
use Leeto\MoonShine\Menu\MenuSection;
use Throwable;

final class MoonShine
{
    public const DIR = 'app/MoonShine';

    public const NAMESPACE = 'App\MoonShine';

    protected static ?Collection $resources = null;

    protected static ?Collection $menu = null;

    public static function path(string $path = ''): string
    {
        return realpath(
            dirname(__DIR__).($path ? DIRECTORY_SEPARATOR.$path : $path)
        );
    }

    public static function dir(string $path = ''): string
    {
        return (config('moonshine.dir') ?? self::DIR).$path;
    }

    public static function namespace(string $path = ''): string
    {
        return (config('moonshine.namespace') ?? self::NAMESPACE).$path;
    }

    public static function getResourceFromUriKey(string $uri): ResourceContract
    {
        $resource = MoonShine::getResources()
            ->first(fn(ResourceContract $resource) => $resource->uriKey() === $uri);

        if ($resource) {
            return $resource;
        }

        $class = (string) str($uri)
            ->studly()
            ->whenStartsWith(
                'MoonShine',
                fn(Stringable $str) => $str->prepend('Leeto\MoonShine\Resources\\'),
                fn(Stringable $str) => $str->prepend(MoonShine::namespace('\Resources\\')),
            );

        return new $class;
    }

    /**
     * Register resource classes in the system
     *
     * @param  array  $data  Array of resource classes that is registering
     * @return void
     * @throws Throwable
     */
    public static function resources(array $data): void
    {
        self::$resources = collect();

        collect($data)->each(function ($item) {
            $item = is_string($item) ? new $item() : $item;

            if (!$item instanceof ResourceContract) {
                throw MoonShineException::onlyResourceAllowed();
            }

            self::$resources->add($item);
        });

        self::resolveResourcesRoutes();
    }

    /**
     * @throws MenuException
     */
    public static function menu(array $data): void
    {
        self::$menu = collect();

        collect($data)->each(
            function ($item) {
                $item = is_string($item) ? new $item() : $item;

                if (!$item instanceof MenuSection) {
                    throw MenuException::onlyMenuItemAllowed();
                }

                self::$menu->add($item);
            }
        );

        Menu::register(self::$menu);
    }

    /**
     * Get collection of registered resources
     *
     * @return Collection<ResourceContract>
     */
    public static function getResources(): Collection
    {
        return self::$resources;
    }

    /**
     * Register moonshine routes and resources routes in the system
     *
     * @return void
     */
    protected static function resolveResourcesRoutes(): void
    {
        Route::prefix(config('moonshine.prefix'))
            ->middleware(['moonshine', 'auth:moonshine'])
            ->name(config('moonshine.prefix').'.')->group(function () {
                self::getResources()->each(function (ResourceContract $resource) {
                    $resource->resolveRoutes();
                });
            });
    }
}
