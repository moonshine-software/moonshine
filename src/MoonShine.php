<?php
namespace Leeto\MoonShine;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Leeto\MoonShine\Controllers\MoonShineUserRoleController;
use Leeto\MoonShine\Controllers\MoonShineUserController;
use Leeto\MoonShine\Controllers\IndexController;
use Leeto\MoonShine\Menu\Menu;
use Leeto\MoonShine\Menu\MenuGroup;
use Leeto\MoonShine\Menu\MenuItem;
use Leeto\MoonShine\Resources\BaseResource;

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
        Route::prefix('moonshine')
            ->middleware(config("moonshine.route.middleware"))
            ->name("moonshine.")->group(function () {

            Route::get("/", [IndexController::class, 'index'])->name("index");

            Route::any("/login", [IndexController::class, 'login'])->name("login");
            Route::get("/logout", [IndexController::class, 'logout'])->name("logout");

            Route::post("/attachments", [IndexController::class, 'attachments'])->name("attachments");

            Route::resource("moonshineusers", MoonShineUserController::class);
            Route::resource("moonshineuserroles", MoonShineUserRoleController::class);

            $this->resources->each(function ($resource) {
                /* @var BaseResource $resource */
                Route::resource($resource->routeAlias(), $resource->controllerName());
            });
        });
    }
}