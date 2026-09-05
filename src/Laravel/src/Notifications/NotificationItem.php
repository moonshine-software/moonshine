<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Notifications;

use DateTimeInterface;
use Illuminate\Notifications\DatabaseNotification;
use MoonShine\Contracts\Core\DependencyInjection\RouterContract;
use MoonShine\Crud\Contracts\Notifications\NotificationButtonContract;
use MoonShine\Crud\Contracts\Notifications\NotificationItemContract;
use MoonShine\Crud\Notifications\NotificationButton;

/**
 * @phpstan-type NotificationData array{color?: string|null, message?: string, icon?: string|null, button?: array{label: string, link: string, attributes?: array<string, mixed>}|array{}}
 */
final readonly class NotificationItem implements NotificationItemContract
{
    public function __construct(
        private DatabaseNotification $notification,
    ) {
    }

    public function getId(): int|string|null
    {
        /** @var int|string|null */
        return $this->notification->getKey();
    }

    /** @return NotificationData */
    private function getData(): array
    {
        /** @var NotificationData */
        return $this->notification->data;
    }

    public function getReadRoute(): string
    {
        return app(RouterContract::class)->to('notifications.read', [
            'notification' => $this->notification,
        ]);
    }

    public function getColor(): string
    {
        return $this->getData()['color'] ?? 'green';
    }

    public function getMessage(): string
    {
        return $this->getData()['message'] ?? '';
    }

    public function getDate(): DateTimeInterface
    {
        /** @var DateTimeInterface|null $createdAt */
        $createdAt = $this->notification->getAttribute('created_at');

        return $createdAt ?? now();
    }

    public function getButton(): ?NotificationButtonContract
    {
        $button = $this->getData()['button'] ?? [];

        if ($button === []) {
            return null;
        }

        return new NotificationButton(
            $button['label'],
            $button['link'],
            $button['attributes'] ?? [],
        );
    }

    public function getIcon(): string
    {
        return $this->getData()['icon'] ?? 'information-circle';
    }
}
