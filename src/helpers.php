<?php

declare(strict_types=1);

use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use MoonShine\Components\FormBuilder;
use MoonShine\Components\TableBuilder;
use MoonShine\Menu\Menu;
use MoonShine\MoonShine;
use MoonShine\MoonShineRequest;
use MoonShine\MoonShineRouter;
use MoonShine\Pages\Page;
use MoonShine\Resources\Resource;
use MoonShine\Utilities\AssetManager;

if (! function_exists('tryOrReturn')) {
    function tryOrReturn(Closure $tryCallback, mixed $default = false): mixed
    {
        try {
            $return = $tryCallback();
        } catch (Throwable) {
            $return = $default;
        }

        return $return;
    }
}

if (! function_exists('moonshine')) {
    function moonshine(): MoonShine
    {
        return app(MoonShine::class);
    }
}

if (! function_exists('to_page')) {
    function to_page(
        string|Resource $resource,
        string|Page|null $page = null,
        bool $redirect = false,
        array $params = []
    ): RedirectResponse|string {
        return MoonShineRouter::to_page($resource, $page, $redirect, $params);
    }
}

if (! function_exists('moonshineRequest')) {
    function moonshineRequest(): MoonShineRequest
    {
        return app(MoonShineRequest::class);
    }
}

if (! function_exists('moonshineAssets')) {
    function moonshineAssets(): AssetManager
    {
        return app(AssetManager::class);
    }
}

if (! function_exists('moonshineMenu')) {
    function moonshineMenu(): Menu
    {
        return app(Menu::class);
    }
}

if (! function_exists('form')) {
    function form(
        string $action = '',
        string $method = 'POST',
        array $fields = [],
        array $values = []
    ): FormBuilder {
        return FormBuilder::make($action, $method, $fields, $values);
    }
}

if (! function_exists('table')) {
    function table(
        array $fields = [],
        iterable $items = [],
        ?LengthAwarePaginator $paginator = null
    ): TableBuilder {
        return TableBuilder::make($fields, $items, $paginator);
    }
}
