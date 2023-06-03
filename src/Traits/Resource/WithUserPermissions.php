<?php

declare(strict_types=1);

namespace MoonShine\Traits\Resource;

use Illuminate\Support\Facades\Route;
use MoonShine\Http\Controllers\PermissionController;

trait WithUserPermissions
{
    public function resolveRoutes(): void
    {
        parent::resolveRoutes();

        Route::prefix('resource')->group(function (): void {
            Route::post(
                "{$this->uriKey()}/{" . $this->routeParam() . "}/permissions",
                PermissionController::class
            )->name("{$this->routeNameAlias()}.permissions");
        });
    }
}
