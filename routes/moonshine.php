<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use MoonShine\Http\Controllers\AsyncController;
use MoonShine\Http\Controllers\AuthenticateController;
use MoonShine\Http\Controllers\CrudController;
use MoonShine\Http\Controllers\GlobalSearchController;
use MoonShine\Http\Controllers\HandlerController;
use MoonShine\Http\Controllers\HomeController;
use MoonShine\Http\Controllers\NotificationController;
use MoonShine\Http\Controllers\PageController;
use MoonShine\Http\Controllers\ProfileController;
use MoonShine\Http\Controllers\RelationModelFieldController;
use MoonShine\Http\Controllers\SocialiteController;
use MoonShine\Http\Controllers\UpdateFieldController;

$authMiddleware = moonshineConfig()->getAuthMiddleware();

Route::moonshine(static function (Router $router) use($authMiddleware): void {
    $router->middleware(
        $authMiddleware
    )->group(function (): void {
        Route::prefix('resource/{resourceUri}')->group(function (): void {
            Route::delete('crud', [CrudController::class, 'massDelete'])->name('crud.massDelete');

            Route::resource('crud', CrudController::class)
                ->parameter('crud', 'resourceItem')
                ->only(['store', 'update', 'destroy']);

            Route::any('handler/{handlerUri}', HandlerController::class)->name('handler');
            Route::get('{pageUri}', PageController::class)->name('resource.page');
        });

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

        Route::get(
            'search',
            GlobalSearchController::class
        )->name('global-search');

        Route::prefix('relation/{pageUri}')->controller(RelationModelFieldController::class)->group(
            function (): void {
                Route::get('{resourceUri?}/{resourceItem?}', 'search')->name('relation.search');
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
    });

    if (moonshineConfig()->isAuthEnabled()) {
        Route::controller(AuthenticateController::class)->group(static function (): void {
            Route::get('/login', 'login')->name('login');
            Route::post('/authenticate', 'authenticate')->name('authenticate');
            Route::get('/logout', 'logout')->name('logout');
        });

        Route::controller(SocialiteController::class)
            ->prefix('socialite')
            ->as('socialite.')
            ->group(static function (): void {
                Route::get('/{driver}/redirect', 'redirect')->name('redirect');
                Route::get('/{driver}/callback', 'callback')->name('callback');
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
