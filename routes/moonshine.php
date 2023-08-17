<?php

use Illuminate\Support\Facades\Route;
use MoonShine\Exceptions\MoonShineNotFoundException;
use MoonShine\Http\Controllers\ActionController;
use MoonShine\Http\Controllers\AttachmentController;
use MoonShine\Http\Controllers\AuthenticateController;
use MoonShine\Http\Controllers\CrudController;
use MoonShine\Http\Controllers\DashboardController;
use MoonShine\Http\Controllers\NotificationController;
use MoonShine\Http\Controllers\PageController;
use MoonShine\Http\Controllers\ProfileController;
use MoonShine\Http\Controllers\SearchController;
use MoonShine\Http\Controllers\SocialiteController;

Route::prefix(config('moonshine.route.prefix', ''))
    ->middleware('moonshine')
    ->as('moonshine.')->group(static function () {
        Route::middleware(config('moonshine.auth.middleware'))->group(function (): void {

            Route::prefix('resource/{resourceUri}')->group(function (): void {
                Route::delete('crud', [CrudController::class, 'massDelete'])->name('crud.massDelete');

                Route::resource('crud', CrudController::class)
                    ->parameter('crud', 'resourceItem')
                    ->only(['store', 'update', 'destroy']);

                Route::any('actions', ActionController::class)->name('actions');

                Route::get('{pageUri}', PageController::class)->name('page');
            });


            Route::get('/', DashboardController::class)->name('index');
            Route::post('/attachments', AttachmentController::class)->name('attachments');

            Route::get('/search/relations', [SearchController::class, 'relations'])
                ->name('search.relations');

            Route::controller(NotificationController::class)
                ->prefix('notifications')
                ->as('notifications.')
                ->group(static function (): void {
                    Route::get('/', 'readAll')->name('readAll');
                    Route::get('/{notification}', 'read')->name('read');
                });
        });

        if (config('moonshine.auth.enable', true)) {
            Route::controller(AuthenticateController::class)
                ->group(static function (): void {
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
                ->middleware(config('moonshine.auth.middleware'))
                ->name('profile.store');
        }

        Route::fallback(static function () {
            $handler = config(
                'moonshine.route.notFoundHandler',
                MoonShineNotFoundException::class
            );

            throw new $handler();
        });
    });
