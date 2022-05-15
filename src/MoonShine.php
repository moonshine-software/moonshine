<?php
namespace Leeto\MoonShine;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Leeto\MoonShine\Controllers\MoonShineAuthController;
use Leeto\MoonShine\Controllers\MoonShineUserRoleController;
use Leeto\MoonShine\Controllers\MoonShineUserController;
use Leeto\MoonShine\Controllers\MoonShineDashboardController;
use Leeto\MoonShine\Menu\Menu;
use Leeto\MoonShine\Menu\MenuGroup;
use Leeto\MoonShine\Menu\MenuItem;
use Leeto\MoonShine\Models\MoonshineUserRole;
use Leeto\MoonShine\Resources\BaseResource;
use Leeto\MoonShine\Resources\MoonShineUserResource;
use Leeto\MoonShine\Resources\MoonShineUserRoleResource;

class MoonShine
{
    protected Collection|null $resources = null;

    protected Collection|null $menus = null;

    public function registerResources(array $data): void
    {
        $this->resources = collect();
        $this->menus = collect();

        collect($data)->each(function ($item) {
            $item = is_string($item) ? new $item() : $item;

            if($item instanceof BaseResource) {

                $this->resources->add($item);
                $this->menus->add(new MenuItem($item->title(), $item));

            } elseif($item instanceof MenuItem) {
                $this->resources->add($item->resource());
                $this->menus->add($item);

            } elseif($item instanceof MenuGroup) {
                $this->menus->add($item);

                $item->items()->each(function($subItem) {
                    $this->resources->add($subItem->resource());
                });

            }
        });

        app(Menu::class)->register($this->menus);

        $this->addRoutes();
    }

    /* @return BaseResource[] */
    public function getResources(): Collection
    {
        return $this->resources;
    }

    protected function addRoutes(): void
    {
        Route::prefix(config('moonshine.route.prefix'))
            ->middleware(config('moonshine.route.middleware'))
            ->name(config('moonshine.route.prefix') . '.')->group(function () {

            Route::get('/', [MoonShineDashboardController::class, 'index'])->name('index');
            Route::post('/attachments', [MoonShineDashboardController::class, 'attachments'])->name('attachments');

            Route::any('/login', [MoonShineAuthController::class, 'login'])->name('login');
            Route::get('/logout', [MoonShineAuthController::class, 'logout'])->name('logout');

            Route::resource((new MoonShineUserResource())->routeAlias(), MoonShineUserController::class);
            Route::resource((new MoonshineUserRoleResource())->routeAlias(), MoonShineUserRoleController::class);

            $this->resources->each(function ($resource) {
                /* @var BaseResource $resource */
                Route::resource($resource->routeAlias(), $resource->controllerName());
            });
        });
    }
}