<?php

use Illuminate\Support\Facades\Route;
use Leeto\MoonShine\Http\Controllers\AttachmentController;
use Leeto\MoonShine\Http\Controllers\AuthenticateController;
use Leeto\MoonShine\Http\Controllers\CrudController;
use Leeto\MoonShine\Http\Controllers\CustomPageController;
use Leeto\MoonShine\Http\Controllers\DashboardController;
use Leeto\MoonShine\Http\Controllers\NotificationController;
use Leeto\MoonShine\Http\Controllers\ProfileController;
use Leeto\MoonShine\Http\Controllers\SocialiteController;

Route::prefix(config('moonshine.route.prefix', ''))
    ->middleware(config('moonshine.route.middleware'))
    ->as('moonshine.')->group(static function () {
        Route::get('/', DashboardController::class)->name('index');
        Route::post('/attachments', AttachmentController::class)->name('attachments');

        Route::put("update-column", [CrudController::class, 'updateColumn'])
            ->name("update-column");

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
    });
