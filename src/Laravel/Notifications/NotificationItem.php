<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Notifications;

use DateTimeInterface;
use Illuminate\Notifications\DatabaseNotification;

final readonly class NotificationItem implements NotificationItemContract
{
    public function __construct(
        private DatabaseNotification $notification,
        private int $index = 0
    ) {
    }

    public function getReadRoute(): string
    {
        return route('moonshine.notifications.read', $this->notification);
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getColor(): string
    {
        return $this->notification->data['color'] ?? 'green';
    }

    public function getMessage(): string
    {
        return $this->notification->data['message'] ?? '';
    }

    public function getDate(): DateTimeInterface
    {
        return $this->notification->created_at ?? now();
    }

    public function getButton(): array
    {
        return $this->notification->data['button'] ?? [];
    }

    public function getButtonLink(): ?string
    {
        return data_get($this->getButton(), 'link');
    }

    public function getButtonLabel(): ?string
    {
        return data_get($this->getButton(), 'label');
    }

    public function getIcon(): string
    {
        return 'information-circle';
    }
}
