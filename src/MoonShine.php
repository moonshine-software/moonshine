<?php

declare(strict_types=1);

namespace Leeto\MoonShine;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Leeto\MoonShine\Http\Controllers\ResourceController;
use Leeto\MoonShine\Menu\Menu;
use Leeto\MoonShine\Menu\MenuGroup;
use Leeto\MoonShine\Menu\MenuItem;
use Leeto\MoonShine\Resources\CustomPage;
use Leeto\MoonShine\Resources\Resource;

class MoonShine
{
    public const DIR = 'app/MoonShine';

    public const NAMESPACE = 'App\MoonShine';

    protected Collection|null $resources = null;

    protected Collection|null $pages = null;

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
        $this->pages = collect();
        $this->menus = collect();

        collect($data)->each(function ($item) {
            $item = is_string($item) ? new $item() : $item;

            if ($item instanceof Resource) {
                $this->resources->add($item);
                $this->menus->add(new MenuItem($item->title(), $item));
            } elseif ($item instanceof CustomPage) {
                $this->pages->add($item);
                $this->menus->add(new MenuItem($item->label(), $item));
            } elseif ($item instanceof MenuItem) {
                $this->resources->when($item->resource(), fn($r) => $r->add($item->resource()));
                $this->pages->when($item->page(), fn($r) => $r->add($item->page()));
                $this->menus->add($item);
            } elseif ($item instanceof MenuGroup) {
                $this->menus->add($item);

                $item->items()->each(function ($subItem) {
                    $this->pages->when($subItem->page(), fn($r) => $r->add($subItem->page()));
                    $this->resources->when($subItem->resource(), fn($r) => $r->add($subItem->resource()));
                });
            }
        });

        $this->pages->add(
            CustomPage::make(__('moonshine::ui.profile'), 'profile', 'moonshine::profile')
        );

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
     * Get collection of registered pages
     *
     * @return Collection<CustomPage>
     */
    public function getPages(): Collection
    {
        return $this->pages;
    }

    /**
     * Register moonshine routes and resources routes in the system
     *
     * @return void
     */
    protected function addRoutes(): void
    {
        Route::prefix(config('moonshine.route.prefix', ''))
            ->middleware(config('moonshine.route.middleware'))
            ->as('moonshine.')->group(function () {
                $this->resources->each(static function (Resource $resource): void {
                    $alias = $resource->routeAlias();
                    $parameter = $resource->routeParam();
                    $controllerName = $resource->controllerName();

                    Route::controller(ResourceController::class)
                        ->prefix($alias)
                        ->as("$alias.")
                        ->group(static function () use ($parameter) {
                            Route::any('actions', 'actions')->name('actions');
                            Route::get("form-action/$parameter/{index}", 'formAction')->name('form-action');
                            Route::get("action/$parameter/{index}", 'action')->name('action');
                            Route::post('bulk/{index}', 'bulk')->name('bulk');
                            Route::get('query-tag/{uri}', 'index')->name('query-tag');
                        });


                    if ($resource->isSystem()) {
                        Route::resource($alias, $controllerName);
                    } else {
                        Route::resource($alias, ResourceController::class);
                    }

                    if ($alias === 'moonShineUsers') {
                        Route::post("$alias/permissions/{".$parameter."}", [$controllerName, 'permissions'])
                            ->name("$alias.permissions");
                    }
                });
            });
    }

    public static function changeLogs(Model $item): ?Collection
    {
        if (!isset($item->changeLogs) || !$item->changeLogs instanceof Collection) {
            return null;
        }

        if ($item->changeLogs->isNotEmpty()) {
            return $item->changeLogs->filter(static function ($log) {
                return $log->states_after;
            });
        }

        return null;
    }
}
