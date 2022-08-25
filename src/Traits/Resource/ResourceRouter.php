<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits\Resource;

use Illuminate\Support\Facades\Route;
use Leeto\MoonShine\Http\Controllers\ResourceController;
use Leeto\MoonShine\MoonShine;
use Stringable;

trait ResourceRouter
{
    public function resolveRoutes(): void
    {
        Route::delete(
            "{$this->uriKey()}/massDelete",
            [ResourceController::class, 'massDelete']
        )->name("{$this->routeAlias()}.massDelete");

        Route::resource($this->uriKey(), ResourceController::class)
            ->parameters([$this->uriKey() => $this->routeParam()])
            ->names($this->routeAlias());
    }

    public function routeAlias(): string
    {
        return (string) str(static::class)
            ->classBasename()
            ->replace(['Resource'], '')
            ->plural()
            ->lcfirst();
    }

    public function routeParam(): string
    {
        return (string) str($this->routeAlias())->singular();
    }

    public function routeName(?string $action = null): string
    {
        return (string) str(config('moonshine.prefix'))
            ->append('.')
            ->append($this->routeAlias())
            ->when($action, fn($str) => $str->append('.')->append($action));
    }

    public function route(string $action, int $id = null, array $query = []): string
    {
        return route(
            $this->routeName($action),
            $id ? array_merge([$this->routeParam() => $id], $query) : $query
        );
    }

    public function controllerName(): string
    {
        return (string) str(static::class)
            ->classBasename()
            ->replace(['Resource'], '')
            ->append('Controller')
            ->whenContains(
                ['MoonShine'],
                fn(Stringable $str) => $str->prepend('Leeto\MoonShine\Http\Controllers\\'),
                fn(Stringable $str) => $str->prepend(MoonShine::namespace('\Controllers\\'))
            );
    }

    public function uriKey(): string
    {
        return str(class_basename(get_called_class()))
            ->kebab()
            ->value();
    }
}
