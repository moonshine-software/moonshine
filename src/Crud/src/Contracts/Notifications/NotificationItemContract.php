<?php

declare(strict_types=1);

namespace MoonShine\Crud\Contracts\Notifications;

use DateTimeInterface;

interface NotificationItemContract
{
    public function getId(): string|int|null;

    public function getReadRoute(): string;

    public function getColor(): string;

    public function getMessage(): string;

    public function getIcon(): string;

    public function getDate(): DateTimeInterface;

    public function getButton(): ?NotificationButtonContract;
}
