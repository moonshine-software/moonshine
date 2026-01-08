<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use MoonShine\Laravel\DefaultRoutes;

if (moonshineConfig()->isUseRoutes()) {
    Route::moonshine(static fn (Router $router, DefaultRoutes $defaultRoutes) => $defaultRoutes($router));
}
