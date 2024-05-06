<?php

declare(strict_types=1);

use Illuminate\Cache\Repository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use MoonShine\Applies\AppliesRegister;
use MoonShine\AssetManager\AssetManager;
use MoonShine\ColorManager\ColorManager;
use MoonShine\Components\ActionButtons\ActionButton;
use MoonShine\Components\FormBuilder;
use MoonShine\Components\TableBuilder;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Exceptions\MoonShineNotFoundException;
use MoonShine\Fields\Field;
use MoonShine\Fields\Fields;
use MoonShine\MenuManager\MenuManager;
use MoonShine\MoonShine;
use MoonShine\MoonShineRequest;
use MoonShine\MoonShineRouter;
use MoonShine\Pages\Page;
use MoonShine\Support\Backtrace;
use MoonShine\Support\MemoizeRepository;
use MoonShine\Support\SelectOptions;

if (! function_exists('moonshine')) {
    function moonshine(): MoonShine
    {
        return app(MoonShine::class);
    }
}

if (! function_exists('appliesRegister')) {
    function appliesRegister(): AppliesRegister
    {
        return app(AppliesRegister::class);
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

if (! function_exists('moonshineColors')) {
    function moonshineColors(): ColorManager
    {
        return app(ColorManager::class);
    }
}

if (! function_exists('moonshineMenu')) {
    function moonshineMenu(): MenuManager
    {
        return app(MenuManager::class);
    }
}

if (! function_exists('moonshineRouter')) {
    function moonshineRouter(): MoonShineRouter
    {
        return app(MoonShineRouter::class);
    }
}

if (! function_exists('moonshineCache')) {
    function moonshineCache(): Repository
    {
        return app('cache')
            ->store(
                app()->runningUnitTests()
                    ? 'array'
                    : config('moonshine.cache', 'file')
            );
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
        Closure|string $url = '',
        mixed $item = null
    ): ActionButton {
        return ActionButton::make($label, $url, $item);
    }
}

if (! function_exists('formErrors')) {
    function formErrors(
        ViewErrorBag|bool $errors,
        ?string $name = null
    ): ViewErrorBag|MessageBag {
        if (! $errors) {
            return new ViewErrorBag();
        }

        if (is_null($name) || ! $errors->hasBag($name)) {
            return $errors;
        }

        return $errors->{$name};
    }
}

if (! function_exists('to_page')) {
    /**
     * @throws Throwable
     */
    function to_page(
        string|Page|null $page = null,
        string|ResourceContract|null $resource = null,
        array $params = [],
        bool $redirect = false,
        ?string $fragment = null
    ): RedirectResponse|string {
        return moonshineRouter()->toPage(
            page: $page,
            resource: $resource,
            params: $params,
            redirect: $redirect,
            fragment: $fragment
        );
    }
}

if (! function_exists('memoize')) {
    /**
     * @template T
     *
     * @param callable(): T $callback
     * @return T
     */
    function memoize(callable $callback): mixed
    {
        $trace = debug_backtrace(
            DEBUG_BACKTRACE_PROVIDE_OBJECT,
            2
        );

        $backtrace = new Backtrace($trace);

        if ($backtrace->getFunctionName() === 'eval') {
            return $callback();
        }

        $object = $backtrace->getObject();

        $hash = $backtrace->getHash();

        $cache = MemoizeRepository::getInstance();

        if (is_string($object)) {
            $object = $cache;
        }

        if (! $cache->isEnabled()) {
            return $callback($backtrace->getArguments());
        }

        if (! $cache->has($object, $hash)) {
            $result = $callback($backtrace->getArguments());

            $cache->set($object, $hash, $result);
        }

        return $cache->get($object, $hash);
    }
}

if (! function_exists('is_closure')) {
    function is_closure(mixed $variable): bool
    {
        return $variable instanceof Closure;
    }
}

if (! function_exists('is_field')) {
    function is_field(mixed $variable): bool
    {
        return $variable instanceof Field;
    }
}

if (! function_exists('is_selected_option')) {
    /**
     * @throws JsonException
     */
    function is_selected_option(mixed $current, string $value): bool
    {
        return SelectOptions::isSelected($current, $value);
    }
}

if (! function_exists('oops404')) {
    function oops404(): never
    {
        $handler = config(
            'moonshine.route.notFoundHandler',
            MoonShineNotFoundException::class
        );

        throw new $handler();
    }
}
