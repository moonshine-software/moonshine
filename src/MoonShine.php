<?php

declare(strict_types=1);

namespace Leeto\MoonShine;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Leeto\MoonShine\Menu\Menu;
use Leeto\MoonShine\Menu\MenuGroup;
use Leeto\MoonShine\Menu\MenuItem;
use Leeto\MoonShine\Resources\Resource;

class MoonShine
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

            if ($item instanceof Resource) {
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
     * @return Collection<Resource>
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
            ->middleware(['moonshine', 'auth:sanctum'])
            ->name(config('moonshine.prefix').'.')->group(function () {
                $this->getResources()->each(function (Resource $resource) {
                    $resource->resolveRoutes();
                });
            });
    }
}
