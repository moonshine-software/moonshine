<?php

declare(strict_types=1);

namespace Leeto\MoonShine;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Stringable;
use Leeto\MoonShine\Contracts\ResourceContract;
use Leeto\MoonShine\Menu\Menu;
use Leeto\MoonShine\Menu\MenuGroup;
use Leeto\MoonShine\Menu\MenuItem;

final class MoonShine
{
    public const DIR = 'app/MoonShine';

    public const NAMESPACE = 'App\MoonShine';

    protected ?Collection $resources = null;

    protected ?Collection $menus = null;

    public static function path(string $path = ''): string
    {
        return realpath(
            dirname(__DIR__).($path ? DIRECTORY_SEPARATOR.$path : $path)
        );
    }

    public static function namespace(string $path = ''): string
    {
        return static::NAMESPACE.$path;
    }

    public static function getResourceFromUri(string $uri): ResourceContract
    {
        $resource = app(MoonShine::class)->getResources()
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
     */
    public function registerResources(array $data): void
    {
        $this->resources = collect();
        $this->menus = collect();

        collect($data)->each(function ($item) {
            $item = is_string($item) ? new $item() : $item;

            if ($item instanceof ResourceContract) {
                $this->resources->add($item);
                $this->menus->add(new MenuItem($item->title(), $item));
            } elseif ($item instanceof MenuItem) {
                $this->resources->add($item->resource());
                $this->menus->add($item);
            } elseif ($item instanceof MenuGroup) {
                $this->menus->add($item);

                $item->items()->each(function ($subItem) {
                    $this->resources->add($subItem->resource());
                });
            }
        });

        app(Menu::class)->register($this->menus);

        $this->resolveResourcesRoutes();
    }

    /**
     * Get collection of registered resources
     *
     * @return Collection<ResourceContract>
     */
    public function getResources(): Collection
    {
        return $this->resources;
    }

    /**
     * Register moonshine routes and resources routes in the system
     *
     * @return void
     */
    protected function resolveResourcesRoutes(): void
    {
        Route::prefix(config('moonshine.prefix'))
            ->middleware(['moonshine', 'auth:moonshine'])
            ->name(config('moonshine.prefix').'.')->group(function () {
                $this->getResources()->each(function (ResourceContract $resource) {
                    $resource->resolveRoutes();
                });
            });
    }
}
