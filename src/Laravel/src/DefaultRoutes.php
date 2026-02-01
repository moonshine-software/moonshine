<?php

declare(strict_types=1);

namespace MoonShine\Laravel;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use MoonShine\Contracts\Core\DependencyInjection\ConfiguratorContract;
use MoonShine\Laravel\Http\Controllers\AsyncSearchController;
use MoonShine\Laravel\Http\Controllers\AuthenticateController;
use MoonShine\Laravel\Http\Controllers\BelongsToManyPivotController;
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

final readonly class DefaultRoutes
{
    public function __construct(
        private ConfiguratorContract $config,
    ) {
    }

    public function __invoke(Router $router, array $config = []): void
    {
        $authEnabled = data_get($config, 'auth.enabled', $this->config->isAuthEnabled());
        $profileEnabled = data_get($config, 'use_profile', $this->config->isUseProfile());
        $authMiddleware = data_get($config, 'auth.middleware', $this->config->getAuthMiddleware());

        $pagePrefix = data_get($config, 'page_prefix', $this->config->getPagePrefix());
        $resourcePrefix = data_get($config, 'resource_prefix', $this->config->getResourcePrefix());
        $errorHandler = data_get($config, 'not_found_exception', $this->config->getNotFoundException());

        if ($authEnabled) {
            Route::controller(AuthenticateController::class)->group(static function (): void {
                Route::get('/login', 'login')->name('login');
                Route::post('/authenticate', 'authenticate')->name('authenticate');
                Route::delete('/logout', 'logout')->name('logout');
            });
        }

        $router->middleware($authMiddleware)->group(function () use ($pagePrefix, $resourcePrefix, $profileEnabled): void {
            if ($profileEnabled) {
                Route::post('/profile', [ProfileController::class, 'store'])
                    ->name('profile.store');
            }

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
             * Pivot modal mode for BelongsToMany field
             * @see BelongsToMany::pivotModalMode()
             */
            Route::prefix('belongs-to-many-pivot')->as('belongs-to-many-pivot.')->controller(BelongsToManyPivotController::class)->group(
                function (): void {
                    Route::get('form/{pageUri}/{resourceUri?}/{resourceItem?}', 'formComponent')
                        ->name('form');
                    Route::post('store/{pageUri}/{resourceUri?}/{resourceItem?}', 'store')
                        ->name('store');
                    Route::put('update/{pageUri}/{resourceUri?}/{resourceItem?}', 'update')
                        ->name('update');
                    Route::delete('destroy/{pageUri}/{resourceUri?}/{resourceItem?}', 'destroy')
                        ->name('destroy');
                    Route::get('list/{pageUri}/{resourceUri?}/{resourceItem?}', 'listComponent')
                        ->name('list');
                }
            );

            /**
             * @see EndpointsContract::toPage()
             */
            Route::get(
                ltrim("/$pagePrefix/{pageUri}", '/'),
                PageController::class
            )->name('page');

            /**
             * CRUD endpoints
             */
            Route::prefix(ltrim("/$resourcePrefix/{resourceUri}", '/'))->group(function (): void {
                Route::delete('crud', [CrudController::class, 'massDelete'])->name('crud.massDelete');

                Route::resource('crud', CrudController::class)->parameter('crud', 'resourceItem');

                Route::any('handler/{handlerUri}', HandlerController::class)->name('handler');

                /**
                 * @see EndpointsContract::toPage()
                 */
                Route::get('{pageUri}/{resourceItem?}', PageController::class)->name('resource.page');
            });
        });

        Route::fallback(static fn (): never => throw new $errorHandler());
    }
}
