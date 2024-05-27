<?php

declare(strict_types=1);

use Illuminate\Cache\Repository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use MoonShine\AssetManager\AssetManager;
use MoonShine\ColorManager\ColorManager;
use MoonShine\Contracts\Collections\FieldsCollection;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Core\Pages\Page;
use MoonShine\Laravel\MoonShineRequest;
use MoonShine\MenuManager\MenuManager;
use MoonShine\MoonShine;
use MoonShine\MoonShineConfigurator;
use MoonShine\MoonShineRouter;
use MoonShine\Support\Enums\FormMethod;
use MoonShine\Support\Memoize\Backtrace;
use MoonShine\Support\Memoize\MemoizeRepository;
use MoonShine\UI\Applies\AppliesRegister;
use MoonShine\UI\Collections\Fields;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\TableBuilder;

if (! function_exists('moonshine')) {
    function moonshine(): MoonShine
    {
        return MoonShine::getInstance();
    }
}

if (! function_exists('appliesRegister')) {
    function appliesRegister(): AppliesRegister
    {
        return moonshine()->getContainer(AppliesRegister::class);
    }
}

if (! function_exists('fieldsCollection')) {
    /**
     * @template-covariant T of FieldsCollection
     * @param  array  $items
     * @param  class-string<T>  $default
     * @return T|Fields
     */
    function fieldsCollection(array $items = [], string $default = Fields::class): FieldsCollection
    {
        return moonshine()
            ->getContainer(FieldsCollection::class, items: $items) ?? $default::make($items);
    }
}

if (! function_exists('moonshineRequest')) {
    function moonshineRequest(): MoonShineRequest
    {
        return moonshine()->getContainer(MoonShineRequest::class);
    }
}

if (! function_exists('moonshineAssets')) {
    function moonshineAssets(): AssetManager
    {
        return moonshine()->getContainer(AssetManager::class);
    }
}

if (! function_exists('moonshineColors')) {
    function moonshineColors(): ColorManager
    {
        return moonshine()->getContainer(ColorManager::class);
    }
}

if (! function_exists('moonshineMenu')) {
    function moonshineMenu(): MenuManager
    {
        return moonshine()->getContainer(MenuManager::class);
    }
}

if (! function_exists('moonshineRouter')) {
    function moonshineRouter(): MoonShineRouter
    {
        return moonshine()->getContainer(MoonShineRouter::class);
    }
}

if (! function_exists('moonshineConfig')) {
    function moonshineConfig(): MoonShineConfigurator
    {
        return moonshine()->getContainer(MoonShineConfigurator::class);
    }
}

if (! function_exists('moonshineCache')) {
    function moonshineCache(): Repository
    {
        return moonshine()->getContainer('cache')
            ->store(moonshineConfig()->getCacheDriver());
    }
}

if (! function_exists('form')) {
    function form(
        string $action = '',
        FormMethod $method = FormMethod::POST,
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

if (! function_exists('toPage')) {
    /**
     * @throws Throwable
     */
    function toPage(
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

if (! function_exists('oops404')) {
    function oops404(): never
    {
        $handler = moonshineConfig()->getNotFoundException();

        throw new $handler();
    }
}
