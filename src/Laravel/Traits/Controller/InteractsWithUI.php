<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Controller;

use MoonShine\Laravel\MoonShineUI;
use MoonShine\Laravel\Notifications\MoonShineNotification;

trait InteractsWithUI
{
    public function toast(string $message, string $type = 'info'): void
    {
        MoonShineUI::toast($message, $type);
    }

    public function notification(
        string $message,
        array $buttons = [],
        array $ids = [],
        ?string $color = null
    ): void {
        MoonShineNotification::send(
            $message,
            $buttons,
            $ids,
            $color
        );
    }
}
