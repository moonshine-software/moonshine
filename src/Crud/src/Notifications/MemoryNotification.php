<?php

declare(strict_types=1);

namespace MoonShine\Crud\Notifications;

use DateTime;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use MoonShine\Crud\Contracts\Notifications\MoonShineNotificationContract;
use MoonShine\Crud\Contracts\Notifications\NotificationButtonContract;
use MoonShine\Support\Enums\Color;

/**
 * @implements MoonShineNotificationContract<NotificationMemoryItem>
 */
final class MemoryNotification implements MoonShineNotificationContract
{
    /**
     * @var array<array-key, NotificationMemoryItem>
     */
    private array $messages = [];

    /**
     * @param  array<int|string>  $ids
     */
    public static function send(
        string $message,
        ?NotificationButtonContract $button = null,
        array $ids = [],
        string|Color|null $color = null,
        ?string $icon = null
    ): void {
        (new self())->notify($message, $button, $ids, $color, $icon);
    }

    /**
     * @param  array<int|string>  $ids
     */
    public function notify(
        string $message,
        ?NotificationButtonContract $button = null,
        array $ids = [],
        string|Color|null $color = null,
        ?string $icon = null
    ): void {
        $color = $color instanceof Color ? $color->value : $color;

        $id = (string) Str::uuid();

        $this->messages[$id] = new NotificationMemoryItem(
            id: $id,
            message: $message,
            color: $color,
            date: new DateTime(),
            button: $button,
            icon: $icon
        );
    }

    /**
     * @return Collection<array-key, NotificationMemoryItem>
     */
    public function getAll(): Collection
    {
        return new Collection($this->messages);
    }

    public function readAll(): void
    {
        $this->messages = [];
    }

    public function markAsRead(int|string $id): void
    {
        data_forget($this->messages, $id);
    }

    public function getReadAllRoute(): string
    {
        return '/';
    }
}
