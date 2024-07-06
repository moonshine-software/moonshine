<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Controller;

use MoonShine\Laravel\MoonShineUI;
use MoonShine\Laravel\Notifications\MoonShineNotification;
use MoonShine\Support\Enums\Color;
use MoonShine\Support\Enums\ToastType;

trait InteractsWithUI
{
    public function toast(string $message, ToastType $type = ToastType::INFO): void
    {
        MoonShineUI::toast($message, $type);
    }

    public function notification(
        string $message,
        array $buttons = [],
        array $ids = [],
        string|Color|null $color = null
    ): void {
        MoonShineNotification::send(
            $message,
            $buttons,
            $ids,
            $color
        );
    }
}
