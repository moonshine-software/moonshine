<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use MoonShine\Crud\Contracts\Notifications\NotificationButtonContract;
use MoonShine\Support\Enums\Color;

final class DatabaseNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected string $message,
        protected ?NotificationButtonContract $button = null,
        protected null|string|Color $color = null,
        protected ?string $icon = null
    ) {
        $this->color = $this->color instanceof Color ? $this->color->value : $this->color;
    }

    /**
     * @return list<string>
     */
    public function via(mixed $notifiable): array
    {
        return ['database'];
    }


    /**
     * @return array{message: string, button: array<string, mixed>, color: string|Color|null, icon: ?string}
     */
    public function toArray(mixed $notifiable): array
    {
        return [
            'message' => $this->message,
            'button' => \is_null($this->button)
                ? []
                : $this->button->toArray(),
            'color' => $this->color,
            'icon' => $this->icon,
        ];
    }
}
