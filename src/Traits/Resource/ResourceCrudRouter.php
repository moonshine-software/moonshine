<?php

declare(strict_types=1);

namespace MoonShine\Traits\Resource;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Http\Controllers\ActionController;
use MoonShine\Http\Controllers\CrudController;
use MoonShine\Http\Controllers\RelationFieldController;
use MoonShine\Http\Controllers\UpdateColumnController;
use MoonShine\Http\Requests\Resources\MassDeleteFormRequest;
use MoonShine\Http\Requests\Resources\ViewAnyFormRequest;

/**
 * @mixin ResourceContract
 */
trait ResourceCrudRouter
{
    public function resolveRoutes(): void
    {
        Route::prefix('resource')->group(function (): void {
            Route::controller(ActionController::class)
                ->prefix($this->uriKey())
                ->as("{$this->routeNameAlias()}.actions.")
                ->group(function (): void {
                    Route::any('action', 'index')->name('index');
                    Route::get(
                        "form/{" . $this->routeParam() . "}/{index}",
                        'form'
                    )->name('form');
                    Route::get(
                        "item/{" . $this->routeParam() . "}/{index}",
                        'item'
                    )->name('item');
                    Route::post('bulk/{index}', 'bulk')->name('bulk');
                });

            Route::get(
                "{$this->uriKey()}/query-tag/{queryTag}",
                fn (
                    ViewAnyFormRequest $request
                ): View|string => (new CrudController())->index($request)
            )
                ->name("{$this->routeNameAlias()}.query-tag");

            Route::delete(
                "{$this->uriKey()}/massDelete",
                fn (
                    MassDeleteFormRequest $request
                ): RedirectResponse => (new CrudController())->massDelete(
                    $request
                )
            )->name("{$this->routeNameAlias()}.massDelete");

            Route::put(
                "{$this->uriKey()}/update-column/{{$this->routeParam()}}",
                UpdateColumnController::class
            )->name("{$this->routeNameAlias()}.update-column");

            Route::get(
                "{$this->uriKey()}/relation-field-items/{{$this->routeParam()}}",
                fn (
                    ViewAnyFormRequest $request
                ): View => (new RelationFieldController())->index($request)
            )->name("{$this->routeNameAlias()}.relation-field-items");

            Route::get(
                "{$this->uriKey()}/relation-field-form/{{$this->routeParam()}?}",
                fn (
                    ViewAnyFormRequest $request
                ): View => (new RelationFieldController())->form($request)
            )->name("{$this->routeNameAlias()}.relation-field-form");

            Route::resource($this->uriKey(), CrudController::class)
                ->parameters([$this->uriKey() => $this->routeParam()])
                ->names($this->routeNameAlias());
        });
    }
}
