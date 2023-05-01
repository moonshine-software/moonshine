<?php

declare(strict_types=1);

namespace MoonShine\Resources;

use Illuminate\Support\Facades\Route;
use MoonShine\Http\Controllers\ActionController;
use MoonShine\Http\Controllers\CrudController;

abstract class SingletonResource extends Resource
{
    protected string $routeAfterSave = 'edit';

    abstract public function getId(): int|string;

    public function resolveRoutes(): void
    {
        Route::prefix('resource')->group(function () {
            Route::controller(ActionController::class)
                ->prefix($this->uriKey())
                ->as("{$this->routeNameAlias()}.actions.")
                ->group(function () {
                    Route::get("form/{".$this->routeParam()."}/{index}", 'form')
                        ->name('form');
                });


            Route::resource($this->uriKey(), CrudController::class)
                ->parameters([$this->uriKey() => $this->routeParam()])
                ->only(['show', 'update', 'edit'])
                ->names($this->routeNameAlias());

            Route::get("{$this->uriKey()}/{$this->getId()}/redirect", function () {
                return redirect($this->route('edit', $this->getId()));
            })->name("{$this->routeNameAlias()}.index");
        });
    }

    public function search(): array
    {
        return [];
    }

    public function filters(): array
    {
        return [];
    }

    public function actions(): array
    {
        return [];
    }
}
