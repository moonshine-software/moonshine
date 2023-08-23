<?php

declare(strict_types=1);

use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Components\FormBuilder;
use MoonShine\Components\TableBuilder;
use MoonShine\Contracts\ApplyContract;
use MoonShine\Fields\Field;
use MoonShine\Fields\Fields;
use MoonShine\Menu\MenuManager;
use MoonShine\MoonShine;
use MoonShine\MoonShineRegister;
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

if (! function_exists('moonshineRegister')) {
    function moonshineRegister(): MoonShineRegister
    {
        return app(MoonShineRegister::class);
    }
}

if (! function_exists('to_page')) {
    function to_page(
        string|Resource|null $resource,
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

if (! function_exists('is_closure')) {
    function is_closure(mixed $variable): bool
    {
        return $variable instanceof Closure;
    }
}

if (! function_exists('moonshineAssets')) {
    function moonshineAssets(): AssetManager
    {
        return app(AssetManager::class);
    }
}

if (! function_exists('moonshineMenu')) {
    function moonshineMenu(): MenuManager
    {
        return app(MenuManager::class);
    }
}

if (! function_exists('form')) {
    function form(
        string $action = '',
        string $method = 'POST',
        Fields|array $fields = [],
        array $values = []
    ): FormBuilder {
        return FormBuilder::make($action, $method, $fields, $values);
    }
}

if (! function_exists('table')) {
    function table(
        Fields|array $fields = [],
        iterable $items = [],
        ?LengthAwarePaginator $paginator = null
    ): TableBuilder {
        return TableBuilder::make($fields, $items, $paginator);
    }
}

if (! function_exists('actionBtn')) {
    function actionBtn(
        Closure|string $label,
        Closure|string|null $url = null,
        mixed $item = null
    ): ActionButton {
        return ActionButton::make($label, $url, $item);
    }
}

if (! function_exists('findFieldApply')) {
    function findFieldApply(Field $field, string $type, string $for): ?ApplyContract
    {
        $applyClass = moonshineRegister()
            ->{$type}()
            ->for($for)
            ->get($field::class);

        return
            ! is_null($applyClass)
            && class_exists($applyClass)
            ? new $applyClass()
            : null;
    }
}


if (! function_exists('formErrors')) {
    function formErrors(
        ViewErrorBag|bool $errors,
        ?string $name = null
    ): ViewErrorBag|MessageBag {
        if(! $errors) {
            return new ViewErrorBag();
        }

        if(is_null($name) || ! $errors->hasBag($name)) {
            return $errors;
        }

        return $errors->{$name};
    }
}
