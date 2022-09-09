<?php

use Illuminate\Support\Facades\Route;
use Leeto\MoonShine\Http\Controllers\AuthController;
use Leeto\MoonShine\Http\Controllers\DashboardController;
use Leeto\MoonShine\Http\Controllers\InitialController;
use Leeto\MoonShine\Http\Controllers\ViewComponentController;
use Leeto\MoonShine\Http\Controllers\ViewController;
use Leeto\MoonShine\Http\Controllers\ViewEntityComponentController;
use Leeto\MoonShine\Http\Controllers\ViewEntityController;

Route::prefix(config('moonshine.prefix'))
    ->middleware(['moonshine'])
    ->name(config('moonshine.prefix').'.')->group(function () {
        Route::get('/initial', InitialController::class)->name('initial');

        Route::get('/view/{resourceUri}/{viewUri}', ViewController::class)
            ->name('view');
        Route::get('/view/{resourceUri}/{viewUri}/{id?}', ViewEntityController::class)
            ->name('view.entity');

        Route::get('/view/{resourceUri}/{viewUri}/{componentUri}', ViewComponentController::class)
            ->name('view-component');
        Route::get('/view/{resourceUri}/{viewUri}/{componentUri}/{id?}', ViewEntityComponentController::class)->name(
            'view-component.entity'
        );

        Route::controller(AuthController::class)->group(function () {
            Route::post('/authenticate', 'authenticate')->name('authenticate');
            Route::delete('/logout', 'logout')->name('logout');
            Route::get('/authenticate', 'me')
                ->middleware(['auth:sanctum'])
                ->name('authenticate.me');
        });

        Route::middleware(['auth:sanctum'])->group(function () {
            Route::get('/', DashboardController::class)->name('dashboard');
        });
    });
