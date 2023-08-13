<?php

declare(strict_types=1);

namespace MoonShine\Traits\Resource;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Stringable;
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
                    //
                });
        });
    }

    public function currentRoute(array $query = []): string
    {
        return str(request()->url())->when(
            $query,
            static fn (Stringable $str) => $str
                ->append('?')
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

        data_forget($query, ['change-moonshine-locale', 'reset']);

        return MoonShineRouter::to(
            str($name)->contains('.') ? $name : 'crud.' . $name,
            $key ? array_merge(['resourceItem' => $key], $query) : $query
        );
    }

    public function redirectAfterSave(): string
    {
        return $this->defaultRedirect();
    }

    public function redirectAfterDelete(): string
    {
        return $this->defaultRedirect();
    }

    protected function defaultRedirect(): string
    {
        return to_page($this, 'index-page');
    }
}
