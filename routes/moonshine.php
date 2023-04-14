<?php

use Illuminate\Support\Facades\Route;
use MoonShine\Exceptions\MoonShineNotFoundException;
use MoonShine\Http\Controllers\AttachmentController;
use MoonShine\Http\Controllers\AuthenticateController;
use MoonShine\Http\Controllers\CrudController;
use MoonShine\Http\Controllers\CustomPageController;
use MoonShine\Http\Controllers\DashboardController;
use MoonShine\Http\Controllers\NotificationController;
use MoonShine\Http\Controllers\ProfileController;
use MoonShine\Http\Controllers\SearchController;
use MoonShine\Http\Controllers\SocialiteController;

Route::prefix(config('moonshine.route.prefix', ''))
    ->middleware(config('moonshine.route.middleware'))
    ->as('moonshine.')->group(static function () {
        Route::get('/', DashboardController::class)->name('index');
        Route::post('/attachments', AttachmentController::class)->name('attachments');

        Route::get('/search/relations', [SearchController::class, 'relations'])
            ->name('search.relations');

        Route::put('update-column', [CrudController::class, 'updateColumn'])
            ->name('update-column');

        Route::controller(NotificationController::class)
            ->prefix('notifications')
            ->as('notifications.')
            ->group(static function () {
                Route::get('/', 'readAll')->name('readAll');
                Route::get('/{notification}', 'read')->name('read');
            });


        if (config('moonshine.auth.enable', true)) {
            Route::controller(AuthenticateController::class)->group(static function () {
                Route::get('/login', 'login')->name('login');
                Route::post('/authenticate', 'authenticate')->name('authenticate');
                Route::get('/logout', 'logout')->name('logout');
            });

            Route::controller(SocialiteController::class)
                ->prefix('socialite')
                ->as('socialite.')
                ->group(static function () {
                    Route::get('/{driver}/redirect', 'redirect')->name('redirect');
                    Route::get('/{driver}/callback', 'callback')->name('callback');
                });

            Route::post('/profile', [ProfileController::class, 'store'])->name('profile.store');
        }

        Route::get(
            config('moonshine.route.custom_page_slug', 'custom_page').'/{alias}',
            CustomPageController::class
        )->name('custom_page');

        Route::fallback(static function () {
            $handler = config(
                'moonshine.route.notFoundHandler',
                MoonShineNotFoundException::class
            );

            throw new $handler();
        });
    });
