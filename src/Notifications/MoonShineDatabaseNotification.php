<?php

declare(strict_types=1);

namespace MoonShine\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use MoonShine\Traits\Makeable;

/**
 * @method static make(string $message, array $button = [], string $color = 'green')
 */
final class MoonShineDatabaseNotification extends Notification
{
    use Queueable;
    use Makeable;

    public function __construct(
        protected string $message,
        protected array $button = [],
        protected ?string $color = null
    ) {
        //
    }

    public function via($notifiable): array
    {
        return ['database'];
    }


    /**
     * @return array{message: string, button: array}
     */
    public function toArray($notifiable): array
    {
        return [
            'message' => $this->message,
            'button' => $this->button,
        ];
    }
}
