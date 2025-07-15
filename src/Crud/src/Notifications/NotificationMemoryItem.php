<?php

declare(strict_types=1);

namespace MoonShine\Crud\Notifications;

use DateTime;
use DateTimeInterface;
use MoonShine\Crud\Contracts\Notifications\NotificationButtonContract;
use MoonShine\Crud\Contracts\Notifications\NotificationItemContract;

final readonly class NotificationMemoryItem implements NotificationItemContract
{
    public function __construct(
        private null|int|string $id,
        private ?string $message,
        private ?string $color = null,
        private ?DateTimeInterface $date = null,
        private ?NotificationButtonContract $button = null,
        private ?string $icon = null
    ) {
    }

    public function getId(): int|string|null
    {
        return $this->id;
    }

    public function getReadRoute(): string
    {
        return '/';
    }

    public function getColor(): string
    {
        return $this->color ?? 'green';
    }

    public function getMessage(): string
    {
        return $this->message ?? '';
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date ?? new DateTime();
    }

    public function getButton(): ?NotificationButtonContract
    {
        return $this->button;
    }

    public function getIcon(): string
    {
        return $this->icon ?? 'information-circle';
    }
}
