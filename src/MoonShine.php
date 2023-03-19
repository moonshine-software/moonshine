<?php

declare(strict_types=1);

namespace Leeto\MoonShine;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Leeto\MoonShine\Http\Controllers\AuthenticateController;
use Leeto\MoonShine\Http\Controllers\CustomPageController;
use Leeto\MoonShine\Http\Controllers\DashboardController;
use Leeto\MoonShine\Http\Controllers\NotificationController;
use Leeto\MoonShine\Http\Controllers\ProfileController;
use Leeto\MoonShine\Http\Controllers\ResourceController;
use Leeto\MoonShine\Http\Controllers\SocialiteController;
use Leeto\MoonShine\Menu\Menu;
use Leeto\MoonShine\Menu\MenuGroup;
use Leeto\MoonShine\Menu\MenuItem;
use Leeto\MoonShine\Resources\CustomPage;
use Leeto\MoonShine\Resources\Resource;

class MoonShine
{
    public const DIR = 'app/MoonShine';

    public const NAMESPACE = 'App\MoonShine';

    protected Collection|null $resources = null;

    protected Collection|null $pages = null;

    protected Collection|null $menus = null;

    public static function path(string $path = ''): string
    {
        return realpath(
            dirname(__DIR__).($path ? DIRECTORY_SEPARATOR.$path : $path)
        );
    }

    public static function dir(string $path = ''): string
    {
        return (config('moonshine.dir') ?? static::DIR).$path;
    }

    public static function namespace(string $path = ''): string
    {
        return (config('moonshine.namespace') ?? static::NAMESPACE).$path;
    }

    /**
     * Register resource classes in the system
     *
     * @param  array  $data  Array of resource classes that is registering
     * @return void
     */
    public function registerResources(array $data): void
    {
        $this->resources = collect();
        $this->pages = collect();
        $this->menus = collect();

        collect($data)->each(function ($item) {
            $item = is_string($item) ? new $item() : $item;

            if ($item instanceof Resource) {
                $this->resources->add($item);
                $this->menus->add(new MenuItem($item->title(), $item));
            } elseif ($item instanceof CustomPage) {
                $this->pages->add($item);
                $this->menus->add(new MenuItem($item->label(), $item));
            } elseif ($item instanceof MenuItem) {
                $this->resources->when($item->resource(), fn ($r) => $r->add($item->resource()));
                $this->pages->when($item->page(), fn ($r) => $r->add($item->page()));
                $this->menus->add($item);
            } elseif ($item instanceof MenuGroup) {
                $this->menus->add($item);

                $item->items()->each(function ($subItem) {
                    $this->pages->when($subItem->page(), fn ($r) => $r->add($subItem->page()));
                    $this->resources->when($subItem->resource(), fn ($r) => $r->add($subItem->resource()));
                });
            }
        });

        $this->pages->add(
            CustomPage::make(__('moonshine::ui.profile'), 'profile', 'moonshine::profile')
        );

        app(Menu::class)->register($this->menus);

        $this->addRoutes();
    }

    /**
     * Get collection of registered resources
     *
     * @return Collection<Resource>
     */
    public function getResources(): Collection
    {
        return $this->resources;
    }

    /**
     * Get collection of registered pages
     *
     * @return Collection<CustomPage>
     */
    public function getPages(): Collection
    {
        return $this->pages;
    }

    /**
     * Register moonshine routes and resources routes in the system
     *
     * @return void
     */
    protected function addRoutes(): void
    {
        Route::prefix(config('moonshine.route.prefix', ''))
            ->middleware(config('moonshine.route.middleware'))
            ->name('moonshine.')->group(function () {
                Route::get('/', [DashboardController::class, 'index'])->name('index');
                Route::post('/attachments', [DashboardController::class, 'attachments'])->name('attachments');
                Route::get('/auto-update', [DashboardController::class, 'autoUpdate'])->name('auto-update');

                Route::get('/notifications', [NotificationController::class, 'readAll'])->name('notifications.readAll');
                Route::get('/notifications/{notification}', [NotificationController::class, 'read'])->name('notifications.read');

                if (config('moonshine.auth.enable', true)) {
                    Route::get('/login', [AuthenticateController::class, 'login'])->name('login');
                    Route::post('/authenticate', [AuthenticateController::class, 'authenticate'])->name('authenticate');
                    Route::get('/logout', [AuthenticateController::class, 'logout'])->name('logout');

                    Route::get('/socialite/{driver}/redirect', [SocialiteController::class, 'redirect'])->name('socialite.redirect');
                    Route::get('/socialite/{driver}/callback', [SocialiteController::class, 'callback'])->name('socialite.callback');

                    Route::post('/profile', [ProfileController::class, 'store'])->name('profile.store');
                }

                $customPageSlug = config('moonshine.route.custom_page_slug', 'custom_page');

                Route::get("/$customPageSlug/{alias}", CustomPageController::class)
                    ->name('custom_page');

                $this->resources->each(function ($resource) {
                    Route::any(
                        $resource->routeAlias().'/actions',
                        [ResourceController::class, 'actions']
                    )->name($resource->routeAlias().'.actions');

                    Route::get(
                        $resource->routeAlias().'/form-action/{'.$resource->routeParam().'}/{index}',
                        [ResourceController::class, 'formAction']
                    )->name($resource->routeAlias().'.form-action');

                    Route::get(
                        $resource->routeAlias().'/action/{'.$resource->routeParam().'}/{index}',
                        [ResourceController::class, 'action']
                    )->name($resource->routeAlias().'.action');

                    Route::post(
                        $resource->routeAlias().'/bulk/{index}',
                        [ResourceController::class, 'bulk']
                    )->name($resource->routeAlias().'.bulk');

                    Route::get(
                        $resource->routeAlias().'/query-tag/{uri}',
                        [ResourceController::class, 'index']
                    )->name($resource->routeAlias().'.query-tag');

                    /* @var Resource $resource */
                    if ($resource->isSystem()) {
                        Route::resource($resource->routeAlias(), $resource->controllerName());
                    } else {
                        Route::resource($resource->routeAlias(), ResourceController::class);
                    }

                    if ($resource->routeAlias() === 'moonShineUsers') {
                        Route::post($resource->routeAlias(). "/permissions/{{$resource->routeParam()}}", [$resource->controllerName(), 'permissions'])
                            ->name($resource->routeAlias().'.permissions');
                    }
                });
            });
    }

    public static function changeLogs(Model $item): ?Collection
    {
        if (! isset($item->changeLogs) || ! $item->changeLogs instanceof Collection) {
            return null;
        }

        if ($item->changeLogs->isNotEmpty()) {
            return $item->changeLogs->filter(static function ($log) {
                return $log->states_after;
            });
        }

        return null;
    }
}
