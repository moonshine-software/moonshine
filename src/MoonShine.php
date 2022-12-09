<?php

declare(strict_types=1);

namespace Leeto\MoonShine;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Leeto\MoonShine\Http\Controllers\MoonShineAuthController;
use Leeto\MoonShine\Http\Controllers\MoonShineDashboardController;
use Leeto\MoonShine\Http\Controllers\MoonShineResourceController;
use Leeto\MoonShine\Menu\Menu;
use Leeto\MoonShine\Menu\MenuGroup;
use Leeto\MoonShine\Menu\MenuItem;
use Leeto\MoonShine\Resources\Resource;

class MoonShine
{
    public const DIR = 'app/MoonShine';

    public const NAMESPACE = 'App\MoonShine';

    protected Collection|null $resources = null;

    protected Collection|null $menus = null;

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
                $this->resources->when($item->resource(), fn($r) => $r->add($item->resource()));
                $this->menus->add($item);
            } elseif ($item instanceof MenuGroup) {
                $this->menus->add($item);

                $item->items()->each(function ($subItem) {
                    $this->resources->when($subItem->resource(), fn($r) => $r->add($subItem->resource()));
                });
            }
        });

        app(Menu::class)->register($this->menus);

        $this->addRoutes();
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
    protected function addRoutes(): void
    {
        Route::prefix(config('moonshine.route.prefix'))
            ->middleware(config('moonshine.route.middleware'))
            ->name(config('moonshine.route.prefix').'.')->group(function () {
                Route::get('/', [MoonShineDashboardController::class, 'index'])->name('index');
                Route::post('/attachments', [MoonShineDashboardController::class, 'attachments'])->name('attachments');
                Route::get('/auto-update', [MoonShineDashboardController::class, 'autoUpdate'])->name('auto-update');

                Route::get('/login', [MoonShineAuthController::class, 'login'])->name('login');
                Route::post('/authenticate', [MoonShineAuthController::class, 'authenticate'])->name('authenticate');
                Route::get('/logout', [MoonShineAuthController::class, 'logout'])->name('logout');

                $this->resources->each(function ($resource) {
                    Route::get(
                        $resource->routeAlias().'/action/{'.$resource->routeParam().'}/{index}',
                        [MoonShineResourceController::class, 'action']
                    )
                        ->name($resource->routeAlias().'.action');

                    Route::post(
                        $resource->routeAlias().'/bulk/{index}',
                        [MoonShineResourceController::class, 'bulk']
                    )
                        ->name($resource->routeAlias().'.bulk');

                    /* @var Resource $resource */
                    if ($resource->isSystem()) {
                        Route::resource($resource->routeAlias(), $resource->controllerName());
                    } else {
                        Route::resource($resource->routeAlias(), MoonShineResourceController::class);
                    }
                });
            });
    }
}
