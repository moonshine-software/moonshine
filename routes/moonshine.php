<?php

use Illuminate\Support\Facades\Route;
use MoonShine\Http\Controllers\AttachmentController;
use MoonShine\Http\Controllers\AuthenticateController;
use MoonShine\Http\Controllers\CrudController;
use MoonShine\Http\Controllers\DashboardController;
use MoonShine\Http\Controllers\HandlerController;
use MoonShine\Http\Controllers\NotificationController;
use MoonShine\Http\Controllers\PageController;
use MoonShine\Http\Controllers\ProfileController;
use MoonShine\Http\Controllers\RelationModelFieldController;
use MoonShine\Http\Controllers\SocialiteController;
use MoonShine\Http\Controllers\UpdateColumnController;

Route::prefix(config('moonshine.route.prefix', ''))
    ->middleware('moonshine')
    ->as('moonshine.')->group(static function () {
        Route::middleware(config('moonshine.auth.middleware', []))->group(function (): void {
            Route::prefix('resource/{resourceUri}')->group(function (): void {
                Route::delete('crud', [CrudController::class, 'massDelete'])->name('crud.massDelete');

                Route::resource('crud', CrudController::class)
                    ->parameter('crud', 'resourceItem')
                    ->only(['store', 'update', 'destroy']);

                Route::any('handler/{handlerUri}', HandlerController::class)->name('handler');
                Route::get('{pageUri}', PageController::class)->name('resource.page');
                Route::put('/{resourceItem}', UpdateColumnController::class)->name('resource.update-column');
            });

            Route::get(
                config('moonshine.route.single_page_prefix', 'page') . "/{pageUri}",
                PageController::class
            )->name('page');

            Route::prefix('relation/{pageUri}')->controller(RelationModelFieldController::class)->group(
                function (): void {
                    Route::get('{resourceUri?}/{resourceItem?}', 'search')->name('relation.search');
                    Route::post('{resourceUri}/{resourceItem?}', 'store')->name('relation.store');
                    Route::put('{resourceUri}/{resourceItem}', 'update')->name('relation.update');
                }
            );

            Route::get('/', DashboardController::class)->name('index');
            Route::post('/attachments', AttachmentController::class)->name('attachments');

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
                ->middleware(config('moonshine.auth.middleware', []))
                ->name('profile.store');
        }

        Route::fallback(static function (): never {
            oops404();
        });
    });
