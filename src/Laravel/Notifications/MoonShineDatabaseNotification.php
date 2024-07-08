<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use MoonShine\Support\Enums\Color;
use MoonShine\Support\Traits\Makeable;

/**
 * @method static static make(string $message, array $button = [], null|Color|string $color = null)
 */
final class MoonShineDatabaseNotification extends Notification
{
    use Queueable;
    use Makeable;

    public function __construct(
        protected string $message,
        protected array $button = [],
        protected null|string|Color $color = null
    ) {
        $this->color = $this->color instanceof Color ? $this->color->value : $this->color;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }


    /**
     * @return array{message: string, button: array, color: ?string}
     */
    public function toArray($notifiable): array
    {
        return [
            'message' => $this->message,
            'button' => $this->button,
            'color' => $this->color,
        ];
    }
}
