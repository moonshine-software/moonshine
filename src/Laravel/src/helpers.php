<?php

declare(strict_types=1);

use Illuminate\Cache\Repository;
use Illuminate\Http\RedirectResponse;
use MoonShine\Contracts\AssetManager\AssetManagerContract;
use MoonShine\Contracts\ColorManager\ColorManagerContract;
use MoonShine\Contracts\Core\DependencyInjection\AppliesRegisterContract;
use MoonShine\Contracts\Core\DependencyInjection\ConfiguratorContract;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\Core\DependencyInjection\RouterContract;
use MoonShine\Contracts\Core\DependencyInjection\StorageContract;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Contracts\MenuManager\MenuManagerContract;
use MoonShine\Laravel\DependencyInjection\MoonShineConfigurator;
use MoonShine\Laravel\DependencyInjection\MoonShineRouter;
use MoonShine\Laravel\MoonShineRequest;
use MoonShine\UI\Applies\AppliesRegister;

if (! function_exists('moonshineRequest')) {
    function moonshineRequest(): MoonShineRequest
    {
        return app(MoonShineRequest::class);
    }
}

if (! function_exists('moonshine')) {
    function moonshine(): CoreContract
    {
        return app(CoreContract::class);
    }
}

if (! function_exists('moonshineCache')) {
    function moonshineCache(): Repository
    {
        return app('cache')->store(moonshineConfig()->getCacheDriver());
    }
}

if (! function_exists('moonshineAssets')) {
    function moonshineAssets(): AssetManagerContract
    {
        return app(AssetManagerContract::class);
    }
}

if (! function_exists('moonshineColors')) {
    function moonshineColors(): ColorManagerContract
    {
        return app(ColorManagerContract::class);
    }
}

if (! function_exists('moonshineMenu')) {
    function moonshineMenu(): MenuManagerContract
    {
        return app(MenuManagerContract::class);
    }
}

if (! function_exists('moonshineRouter')) {
    /**
     * @return MoonShineRouter
     */
    function moonshineRouter(): RouterContract
    {
        return app(RouterContract::class);
    }
}

if (! function_exists('moonshineConfig')) {
    /**
     * @return MoonShineConfigurator
     */
    function moonshineConfig(): ConfiguratorContract
    {
        return app(ConfiguratorContract::class);
    }
}

if (! function_exists('appliesRegister')) {
    /**
     * @return AppliesRegister
     */
    function appliesRegister(): AppliesRegisterContract
    {
        return app(AppliesRegisterContract::class);
    }
}

if (! function_exists('toPage')) {
    /**
     * @throws Throwable
     */
    function toPage(
        string|PageContract|null $page = null,
        string|ResourceContract|null $resource = null,
        array $params = [],
        bool $redirect = false,
        ?string $fragment = null
    ): RedirectResponse|string {
        return moonshineRouter()->getEndpoints()->toPage(
            page: $page,
            resource: $resource,
            params: $params,
            extra: [
                'redirect' => $redirect,
                'fragment' => $fragment,
            ],
        );
    }
}

if (! function_exists('oops404')) {
    function oops404(): never
    {
        $handler = moonshineConfig()->getNotFoundException();

        throw new $handler();
    }
}
