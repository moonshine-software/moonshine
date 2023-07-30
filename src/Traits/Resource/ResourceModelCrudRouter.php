<?php

declare(strict_types=1);

namespace MoonShine\Traits\Resource;

use Illuminate\Support\Facades\Route;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Http\Controllers\ActionController;
use MoonShine\MoonShineRouter;

/**
 * @mixin ResourceContract
 */
trait ResourceModelCrudRouter
{
    public function resolveRoutes(): void
    {
        Route::prefix('resource')->group(function (): void {
            Route::controller(ActionController::class)
                ->prefix($this->uriKey())
                ->as("actions.")
                ->group(function (): void {

                });
        });
    }

    public function currentRoute(array $query = []): string
    {
        return str(request()->url())
            ->when(
                $query,
                static fn ($str) => $str->append('?')
                    ->append(Arr::query($query))
            )->value();
    }

    public function route(
        string $name = null,
        int|string $key = null,
        array $query = []
    ): string {
        if ($query === [] && cache()->has($this->queryCacheKey())) {
            parse_str(cache()->get($this->queryCacheKey(), ''), $query);
        }

        $query['resourceUri'] = $this->uriKey();

        unset($query['change-moonshine-locale'], $query['reset']);

        return MoonShineRouter::to(
            'crud.' . $name,
            $key ? array_merge(['item' => $key], $query) : $query
        );
    }

    public function getRouteAfterSave(): string
    {
        return match ($this->routeAfterSave) {
            'show', 'edit' => $this->route(
                $this->item ? $this->routeAfterSave : 'index',
                $this?->item?->getKey()
            ),
            default => $this->route('index')
        };
    }
}
