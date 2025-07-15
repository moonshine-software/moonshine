<?php

declare(strict_types=1);

namespace MoonShine\Laravel;

use MoonShine\Support\Enums\ToastType;

/**
 * @deprecated Will be removed in 5.0
 */
class MoonShineUI
{
    public static function toast(string $message, ToastType $type = ToastType::INFO, null|int|false $duration = null): void
    {
        toast($message, $type, $duration);
    }
}
