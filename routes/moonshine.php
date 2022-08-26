<?php

use Illuminate\Support\Facades\Route;
use Leeto\MoonShine\Http\Controllers\AuthController;
use Leeto\MoonShine\Http\Controllers\DashboardController;
use Leeto\MoonShine\Http\Controllers\InitialController;

Route::prefix(config('moonshine.prefix'))
    ->middleware(['moonshine'])
    ->name(config('moonshine.prefix').'.')->group(function () {
        Route::controller(AuthController::class)->group(function () {
            Route::post('/authenticate', 'authenticate')->name('authenticate');
            Route::delete('/logout', 'logout')->name('logout');
        });

        Route::middleware(['auth:sanctum'])->group(function () {
            Route::get('/', DashboardController::class)->name('dashboard');
            Route::get('/authenticate', [AuthController::class, 'check'])->name('user_check');

            Route::get('/initial', InitialController::class)->name('initial');
        });
    });
