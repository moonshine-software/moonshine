<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use MoonShine\Contracts\Core\DependencyInjection\EndpointsContract;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\Laravel\Http\Controllers\AsyncSearchController;
use MoonShine\Laravel\Http\Controllers\AuthenticateController;
use MoonShine\Laravel\Http\Controllers\ComponentController;
use MoonShine\Laravel\Http\Controllers\CrudController;
use MoonShine\Laravel\Http\Controllers\HandlerController;
use MoonShine\Laravel\Http\Controllers\HasManyController;
use MoonShine\Laravel\Http\Controllers\HomeController;
use MoonShine\Laravel\Http\Controllers\MethodController;
use MoonShine\Laravel\Http\Controllers\NotificationController;
use MoonShine\Laravel\Http\Controllers\PageController;
use MoonShine\Laravel\Http\Controllers\ProfileController;
use MoonShine\Laravel\Http\Controllers\ReactiveController;
use MoonShine\Laravel\Http\Controllers\UpdateFieldController;
use MoonShine\Laravel\Traits\Fields\WithAsyncSearch;
use MoonShine\UI\Traits\Fields\UpdateOnPreview;

$authMiddleware = moonshineConfig()->getAuthMiddleware();

Route::moonshine(static function (Router $router) use($authMiddleware): void {
    $pagePrefix = moonshineConfig()->getPagePrefix();
    $authEnabled = moonshineConfig()->isAuthEnabled();

    if ($authEnabled) {
        Route::controller(AuthenticateController::class)->group(static function (): void {
            Route::get('/login', 'login')->name('login');
            Route::post('/authenticate', 'authenticate')->name('authenticate');
            Route::get('/logout', 'logout')->name('logout');
        });

        Route::post('/profile', [ProfileController::class, 'store'])
            ->middleware($authMiddleware)
            ->name('profile.store');
    }

    $router->middleware($authMiddleware)->group(function () use($pagePrefix): void {
        /**
         * @see EndpointsContract::home()
         */
        Route::get('/', HomeController::class)->name('index');

        /**
         * Update only the field value via a column or relation
         * @see UpdateOnPreview
         * @see EndpointsContract::updateField()
         */
        Route::prefix('update-field')->as('update-field.')->controller(UpdateFieldController::class)->group(function (): void {
            Route::put('column/{resourceUri}/{resourceItem}', 'throughColumn')
                ->name('through-column');
            Route::put('relation/{resourceUri}/{pageUri}/{resourceItem}', 'throughRelation')
                ->name('through-relation');
        });

        /**
         * @see WithAsyncSearch
         */
        Route::get('async-search/{pageUri}/{resourceUri?}/{resourceItem?}/', AsyncSearchController::class)
            ->name('async-search');

        Route::controller(NotificationController::class)
            ->prefix('notifications')
            ->as('notifications.')
            ->group(static function (): void {
                Route::get('/', 'readAll')->name('readAll');
                Route::get('/{notification}', 'read')->name('read');
            });

        /**
         * @see EndpointsContract::component()
         */
        Route::get('component/{pageUri}/{resourceUri?}', ComponentController::class)->name('component');
        /**
         * @see EndpointsContract::method()
         */
        Route::any('method/{pageUri}/{resourceUri?}', MethodController::class)->name('method');
        /**
         * @see EndpointsContract::reactive()
         */
        Route::post('reactive/{pageUri}/{resourceUri?}/{resourceItem?}', ReactiveController::class)->name('reactive');

        /**
         * Asynchronously getting form component and listing for field
         * @see HasMany
         */
        Route::prefix('has-many')->as('has-many.')->controller(HasManyController::class)->group(
            function (): void {
                Route::get('form/{pageUri}/{resourceUri?}/{resourceItem?}', 'formComponent')
                    ->name('form');
                Route::get('list/{pageUri}/{resourceUri?}/{resourceItem?}', 'listComponent')
                    ->name('list');
            }
        );

        /**
         * @see EndpointsContract::toPage()
         */
        Route::get(
            "/$pagePrefix/{pageUri}",
            PageController::class
        )->name('page');

        /**
         * CRUD endpoints
         */
        Route::prefix('{resourceUri}')->group(function (): void {
            Route::delete('crud', [CrudController::class, 'massDelete'])->name('crud.massDelete');

            Route::resource('crud', CrudController::class)->parameter('crud', 'resourceItem');

            Route::any('handler/{handlerUri}', HandlerController::class)->name('handler');

            /**
             * @see EndpointsContract::toPage()
             */
            Route::get('{pageUri}/{resourceItem?}', PageController::class)->name('resource.page');
        });
    });

    Route::fallback(static function (): never {
        oops404();
    });
});
