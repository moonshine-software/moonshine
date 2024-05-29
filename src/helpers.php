<?php

declare(strict_types=1);

use Illuminate\Http\RedirectResponse;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Core\MoonShineConfigurator;
use MoonShine\Core\MoonShineRouter;
use MoonShine\Core\Pages\Page;
use MoonShine\Core\Storage\FileStorage;
use MoonShine\Core\Storage\StorageContract;
use MoonShine\MoonShine;

if (! function_exists('moonshine')) {
    function moonshine(): MoonShine
    {
        return MoonShine::getInstance();
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

if (! function_exists('moonshineStorage')) {
    function moonshineStorage(...$parameters): StorageContract
    {
        return moonshine()->getContainer(StorageContract::class, new FileStorage(), ...$parameters);
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
