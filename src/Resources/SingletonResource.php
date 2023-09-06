<?php

declare(strict_types=1);

namespace MoonShine\Resources;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Route;
use MoonShine\Http\Controllers\ActionController;
use MoonShine\Http\Controllers\CrudController;
use MoonShine\Http\Controllers\RelationFieldController;

abstract class SingletonResource extends Resource
{
    protected string $routeAfterSave = 'edit';

    protected string $redirectRoute = 'edit'; // show, edit

    public function resolveRoutes(): void
    {
        Route::prefix('resource')->group(function (): void {
            Route::controller(ActionController::class)
                ->prefix($this->uriKey())
                ->as("{$this->routeNameAlias()}.actions.")
                ->group(function (): void {
                    Route::get(
                        "form/{" . $this->routeParam() . "}/{index}",
                        'form'
                    )
                        ->name('form');
                });

            Route::get(
                "{$this->uriKey()}/relation-field-items/{{$this->routeParam()}}",
                [RelationFieldController::class, 'index']
            )->name("{$this->routeNameAlias()}.relation-field-items");

            Route::get(
                "{$this->uriKey()}/relation-field-form/{{$this->routeParam()}?}",
                [RelationFieldController::class, 'form']
            )->name("{$this->routeNameAlias()}.relation-field-form");

            Route::resource($this->uriKey(), CrudController::class)
                ->parameters([$this->uriKey() => $this->routeParam()])
                ->only(['show', 'update', 'edit'])
                ->names($this->routeNameAlias());

            Route::get(
                "{$this->uriKey()}/{$this->getId()}/redirect",
                fn (): RedirectResponse|Redirector => redirect(
                    $this->route($this->redirectRoute(), $this->getId())
                )
            )->name("{$this->routeNameAlias()}.index");
        });
    }

    abstract public function getId(): int|string;

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

    public function redirectRoute(): string
    {
        return $this->redirectRoute;
    }
}
