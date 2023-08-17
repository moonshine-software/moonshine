<?php

declare(strict_types=1);

namespace MoonShine\Traits\Resource;

use Illuminate\Support\Facades\Route;
use MoonShine\Http\Controllers\PermissionController;

trait WithUserPermissions
{
    protected function resolveRoutes(): void
    {
        Route::post(
            'permissions/{resourceItem}',
            PermissionController::class
        )->name('permissions');
    }
}
