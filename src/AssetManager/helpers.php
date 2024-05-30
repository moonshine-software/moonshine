<?php

declare(strict_types=1);

use MoonShine\AssetManager\AssetManager;

if (! function_exists('moonshineAssets')) {
    function moonshineAssets(): AssetManager
    {
        return moonshine()->getContainer(AssetManager::class);
    }
}
