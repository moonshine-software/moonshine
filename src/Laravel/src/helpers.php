<?php

declare(strict_types=1);

use Illuminate\Http\RedirectResponse;
use MoonShine\Contracts\AssetManager\AssetManagerContract;
use MoonShine\Contracts\ColorManager\ColorManagerContract;
use MoonShine\Contracts\Core\DependencyInjection\AppliesRegisterContract;
use MoonShine\Contracts\Core\DependencyInjection\ConfiguratorContract;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\Core\DependencyInjection\CrudRequestContract;
use MoonShine\Contracts\Core\DependencyInjection\RouterContract;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Contracts\MenuManager\MenuManagerContract;
use MoonShine\Laravel\DependencyInjection\MoonShineConfigurator;
use MoonShine\Laravel\DependencyInjection\MoonShineRouter;
use MoonShine\Laravel\MoonShineEndpoints;
use MoonShine\Laravel\MoonShineRequest;
use MoonShine\Support\Enums\ToastType;
use MoonShine\UI\Applies\AppliesRegister;

if (! \function_exists('moonshineRequest')) {
    /**
     * @return MoonShineRequest
     */
    function moonshineRequest(): CrudRequestContract
    {
        /** @var MoonShineRequest */
        return app(CrudRequestContract::class);
    }
}

if (! \function_exists('moonshine')) {
    function moonshine(): CoreContract
    {
        return app(CoreContract::class);
    }
}

if (! \function_exists('moonshineAssets')) {
    function moonshineAssets(): AssetManagerContract
    {
        return app(AssetManagerContract::class);
    }
}

if (! \function_exists('moonshineColors')) {
    function moonshineColors(): ColorManagerContract
    {
        return app(ColorManagerContract::class);
    }
}

if (! \function_exists('moonshineMenu')) {
    function moonshineMenu(): MenuManagerContract
    {
        return app(MenuManagerContract::class);
    }
}

if (! \function_exists('moonshineRouter')) {
    /**
     * @return RouterContract<MoonShineRouter, MoonShineEndpoints>
     */
    function moonshineRouter(): RouterContract
    {
        return app(RouterContract::class);
    }
}

if (! \function_exists('moonshineConfig')) {
    /**
     * @return ConfiguratorContract<MoonShineConfigurator>
     */
    function moonshineConfig(): ConfiguratorContract
    {
        return app(ConfiguratorContract::class);
    }
}

if (! \function_exists('appliesRegister')) {
    /**
     * @return AppliesRegisterContract<AppliesRegister>
     */
    function appliesRegister(): AppliesRegisterContract
    {
        return app(AppliesRegisterContract::class);
    }
}

if (! \function_exists('toast')) {
    function toast(string $message, ToastType $type = ToastType::INFO, null|int|false $duration = null): void
    {
        session()->flash('toast', [
            'type' => $type->value,
            'message' => $message,
            'duration' => $duration === false ? -1 : $duration,
        ]);
    }
}

if (! \function_exists('toPage')) {
    /**
     * @param  class-string<PageContract>|PageContract|null  $page
     * @param  class-string<ResourceContract>|ResourceContract|null  $resource
     * @throws Throwable
     */
    function toPage(
        string|PageContract|null $page = null,
        string|ResourceContract|null $resource = null,
        array $params = [],
        bool $redirect = false,
        null|string|array $fragment = null
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

if (! \function_exists('oops404')) {
    function oops404(): never
    {
        $handler = moonshineConfig()->getNotFoundException();

        throw new $handler();
    }
}
