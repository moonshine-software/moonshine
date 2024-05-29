<?php

declare(strict_types=1);

use MoonShine\Laravel\MoonShineRequest;
use Illuminate\Cache\Repository;

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

if (! function_exists('oops404')) {
    function oops404(): never
    {
        $handler = moonshineConfig()->getNotFoundException();

        throw new $handler();
    }
}
