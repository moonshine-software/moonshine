<?php

declare(strict_types=1);

use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Applies\Filters\ApplyModelContract;
use MoonShine\Components\FormBuilder;
use MoonShine\Components\TableBuilder;
use MoonShine\Fields\Field;
use MoonShine\Menu\Menu;
use MoonShine\MoonShine;
use MoonShine\MoonShineRegister;
use MoonShine\MoonShineRequest;
use MoonShine\MoonShineRouter;
use MoonShine\Pages\Page;
use MoonShine\Resources\ModelResource;
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

if (! function_exists('register')) {
    function register(): MoonShineRegister
    {
        return app(MoonShineRegister::class);
    }
}

if (! function_exists('to_page')) {
    function to_page(
        string|Resource $resource,
        string|Page|null $page = null,
        array $params = [],
        bool $redirect = false,
    ): RedirectResponse|string {
        return MoonShineRouter::to_page($resource, $page, $params, $redirect);
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

if (! function_exists('actionBtn')) {
    function actionBtn(
        string $label,
        Closure|string|null $url = null,
        mixed $item = null
    ): ActionButton {
        return ActionButton::make($label, $url, $item);
    }
}

if (! function_exists('modelApplyFilter')) {
    function modelApplyFilter(Field $filter): ?ApplyModelContract
    {
        $filterApplyClass = register()
            ->filters()
            ->for(ModelResource::class)
            ->get(get_class($filter));

        return
            !is_null($filterApplyClass)
            && class_exists($filterApplyClass)
            ? new $filterApplyClass()
            : null;
    }
}
