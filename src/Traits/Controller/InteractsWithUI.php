<?php

declare(strict_types=1);

namespace MoonShine\Traits\Controller;

use MoonShine\MoonShineUI;
use MoonShine\Notifications\MoonShineNotification;

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
