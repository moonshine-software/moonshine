<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Leeto\MoonShine\Traits\Makeable;

final class MoonShineDatabaseNotification extends Notification
{
    use Queueable;
    use Makeable;

    public function __construct(
        protected string $message,
        protected array $button = []
    ) {
        //
    }

    public function via($notifiable): array
    {
        return ['database'];
    }


    public function toArray($notifiable): array
    {
        return [
            'message' => $this->message,
            'button' => $this->button,
        ];
    }
}
