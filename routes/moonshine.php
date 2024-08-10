<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use MoonShine\Laravel\Http\Controllers\AsyncController;
use MoonShine\Laravel\Http\Controllers\AuthenticateController;
use MoonShine\Laravel\Http\Controllers\CrudController;
use MoonShine\Laravel\Http\Controllers\HandlerController;
use MoonShine\Laravel\Http\Controllers\HomeController;
use MoonShine\Laravel\Http\Controllers\NotificationController;
use MoonShine\Laravel\Http\Controllers\PageController;
use MoonShine\Laravel\Http\Controllers\ProfileController;
use MoonShine\Laravel\Http\Controllers\RelationModelFieldController;
use MoonShine\Laravel\Http\Controllers\UpdateFieldController;

$authMiddleware = moonshineConfig()->getAuthMiddleware();

Route::moonshine(static function (Router $router) use($authMiddleware): void {
    $router->middleware(
        $authMiddleware
    )->group(function (): void {


        Route::prefix('column')->controller(UpdateFieldController::class)->group(function (): void {
            Route::put('resource/{resourceUri}/{resourceItem}', 'column')
                ->name('column.resource.update-column');
            Route::put('relation/{resourceUri}/{pageUri}/{resourceItem}', 'relation')
                ->name('column.relation.update-column');
        });

        Route::get(
            moonshineConfig()->getPagePrefix() . "/{pageUri}",
            PageController::class
        )->name('page');

        Route::prefix('relation/{pageUri}/{resourceUri?}/{resourceItem?}')->controller(RelationModelFieldController::class)->group(
            function (): void {
                Route::get('/has-many-form', 'hasManyForm')->name('relation.has-many-form');
                Route::get('/', 'search')->name('relation.search');
            }
        );

        Route::prefix('relations/{pageUri}')->controller(RelationModelFieldController::class)->group(
            function (): void {
                Route::get('/{resourceUri?}/{resourceItem?}', 'searchRelations')->name('relation.search-relations');
            }
        );

        Route::get('/', HomeController::class)->name('index');

        Route::controller(NotificationController::class)
            ->prefix('notifications')
            ->as('notifications.')
            ->group(static function (): void {
                Route::get('/', 'readAll')->name('readAll');
                Route::get('/{notification}', 'read')->name('read');
            });

        Route::controller(AsyncController::class)
            ->prefix('async')
            ->as('async.')
            ->group(function (): void {
                Route::get('table/{pageUri}/{resourceUri?}', 'table')
                    ->name('table');
                Route::get('component/{pageUri}/{resourceUri?}', 'component')
                    ->name('component');
                Route::any('method/{pageUri}/{resourceUri?}', 'method')
                    ->name('method');
                Route::post('reactive/{pageUri}/{resourceUri?}/{resourceItem?}', 'reactive')
                    ->name('reactive');
            });

        Route::prefix('{resourceUri}')->group(function (): void {
            Route::delete('crud', [CrudController::class, 'massDelete'])->name('crud.massDelete');

            Route::resource('crud', CrudController::class)
                ->parameter('crud', 'resourceItem');

            Route::any('handler/{handlerUri}', HandlerController::class)->name('handler');
            Route::get('{pageUri}/{resourceItem?}', PageController::class)->name('resource.page');
        });
    });

    if (moonshineConfig()->isAuthEnabled()) {
        Route::controller(AuthenticateController::class)->group(static function (): void {
            Route::get('/login', 'login')->name('login');
            Route::post('/authenticate', 'authenticate')->name('authenticate');
            Route::get('/logout', 'logout')->name('logout');
        });

        Route::post('/profile', [ProfileController::class, 'store'])
            ->middleware(
                $authMiddleware
            )
            ->name('profile.store');
    }

    Route::fallback(static function (): never {
        oops404();
    });
});
