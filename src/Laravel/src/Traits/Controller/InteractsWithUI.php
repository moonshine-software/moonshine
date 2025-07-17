<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Controller;

use MoonShine\Crud\Contracts\Notifications\NotificationButtonContract;
use MoonShine\Support\Enums\Color;
use MoonShine\Support\Enums\ToastType;

trait InteractsWithUI
{
    protected function toast(string $message, ToastType $type = ToastType::INFO, null|int|false $duration = null): void
    {
        toast($message, $type, $duration);
    }

    /**
     * @param  array<int|string>  $ids
     */
    protected function notification(
        string $message,
        ?NotificationButtonContract $buttons = null,
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
