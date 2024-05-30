<?php

declare(strict_types=1);

use MoonShine\Core\Contracts\ConfiguratorContract;
use MoonShine\Core\Contracts\StorageContract;
use MoonShine\Core\MoonShineRouter;
use MoonShine\Core\Storage\FileStorage;
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
    function moonshineConfig(): ConfiguratorContract
    {
        return moonshine()->getContainer(ConfiguratorContract::class);
    }
}

if (! function_exists('moonshineStorage')) {
    function moonshineStorage(...$parameters): StorageContract
    {
        return moonshine()->getContainer(StorageContract::class, new FileStorage(), ...$parameters);
    }
}
