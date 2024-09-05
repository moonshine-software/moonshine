<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Controller;

use MoonShine\Laravel\MoonShineUI;
use MoonShine\Support\Enums\Color;
use MoonShine\Support\Enums\ToastType;

trait InteractsWithUI
{
    protected function toast(string $message, ToastType $type = ToastType::INFO): void
    {
        MoonShineUI::toast($message, $type);
    }

    /**
     * @param  array{}|array{'link': string, 'label': string}  $buttons
     * @param  array<int|string>  $ids
     */
    protected function notification(
        string $message,
        array $buttons = [],
        array $ids = [],
        string|Color|null $color = null
    ): void {
        $this->notification->notify(
            $message,
            $buttons,
            $ids,
            $color
        );
    }
}
