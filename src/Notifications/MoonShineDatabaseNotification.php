<?php

declare(strict_types=1);

namespace MoonShine\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use MoonShine\Traits\Makeable;

/**
 * @method static static make(string $message, array $button = [])
 */
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


    /**
     * @return array{message: string, button: mixed[]}
     */
    public function toArray($notifiable): array
    {
        return [
            'message' => $this->message,
            'button' => $this->button,
        ];
    }
}
