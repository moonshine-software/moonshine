<?php

declare(strict_types=1);

use MoonShine\ColorManager\ColorManager;

if (! function_exists('moonshineColors')) {
    function moonshineColors(): ColorManager
    {
        return moonshine()->getContainer(ColorManager::class);
    }
}
