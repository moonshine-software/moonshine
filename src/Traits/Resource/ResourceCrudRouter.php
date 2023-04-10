<?php

declare(strict_types=1);

namespace MoonShine\Traits\Resource;

use Illuminate\Support\Facades\Route;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Http\Controllers\ActionController;
use MoonShine\Http\Controllers\CrudController;

/**
 * @mixin ResourceContract
 */
trait ResourceCrudRouter
{
    public function resolveRoutes(): void
    {
        Route::prefix('resource')->group(function () {
            Route::controller(ActionController::class)
                ->prefix($this->uriKey())
                ->as("{$this->routeNameAlias()}.actions.")
                ->group(function () {
                    Route::any('action', 'index')->name('index');
                    Route::get("form/{".$this->routeParam()."}/{index}", 'form')->name('form');
                    Route::get("item/{".$this->routeParam()."}/{index}", 'item')->name('item');
                    Route::post('bulk/{index}', 'bulk')->name('bulk');
                });

            Route::get("{$this->uriKey()}/query-tag/{queryTag}", [CrudController::class, 'index'])
                ->name("{$this->routeNameAlias()}.query-tag");

            Route::delete(
                "{$this->uriKey()}/massDelete",
                [CrudController::class, 'massDelete']
            )->name("{$this->routeNameAlias()}.massDelete");

            Route::resource($this->uriKey(), CrudController::class)
                ->parameters([$this->uriKey() => $this->routeParam()])
                ->names($this->routeNameAlias());
        });
    }
}
