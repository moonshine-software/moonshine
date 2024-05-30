<?php

declare(strict_types=1);

use Illuminate\Cache\Repository;
use Illuminate\Http\RedirectResponse;
use MoonShine\Core\Contracts\ResourceContract;
use MoonShine\Laravel\MoonShineRequest;
use MoonShine\Laravel\Pages\Page;

if (! function_exists('moonshineRequest')) {
    function moonshineRequest(): MoonShineRequest
    {
        return moonshine()->getContainer(MoonShineRequest::class);
    }
}

if (! function_exists('moonshineCache')) {
    function moonshineCache(): Repository
    {
        return moonshine()->getContainer('cache')
            ->store(moonshineConfig()->getCacheDriver());
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
