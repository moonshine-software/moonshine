<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits\Resource;

use Illuminate\Support\Facades\Route;
use Leeto\MoonShine\Http\Controllers\ActionController;
use Leeto\MoonShine\Http\Controllers\CrudController;

trait ResourceCrudRouter
{
    public function resolveRoutes(): void
    {
        $this->views()->each(function ($class) {
            if ((new \ReflectionClass($class))->hasMethod('resolveRoutes')) {
                $class::resolveRoutes();
            }
        });

        Route::prefix('resource')->group(function () {
            Route::delete(
                "{$this->uriKey()}/massDelete",
                [CrudController::class, 'massDelete']
            )->name("{$this->routeNameAlias()}.massDelete");

            Route::get(
                "{$this->uriKey()}/action/{uri}",
                ActionController::class,
            )->name("{$this->routeNameAlias()}.action");

            Route::resource($this->uriKey(), CrudController::class)
                ->parameters([$this->uriKey() => $this->routeParam()])
                ->names($this->routeNameAlias());
        });
    }

    public function route(string $action = null, int $id = null, array $query = []): string
    {
        return route(
            $this->routeName($action),
            $id ? array_merge([$this->routeParam() => $id], $query) : $query
        );
    }
}
