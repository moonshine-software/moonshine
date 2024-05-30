<?php

declare(strict_types=1);

use MoonShine\MenuManager\MenuManager;

if (! function_exists('moonshineMenu')) {
    function moonshineMenu(): MenuManager
    {
        return moonshine()->getContainer(MenuManager::class);
    }
}
