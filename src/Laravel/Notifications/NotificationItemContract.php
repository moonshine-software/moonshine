<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Notifications;

use DateTimeInterface;

interface NotificationItemContract
{
    public function getReadRoute(): string;

    public function getColor(): string;

    public function getMessage(): string;

    public function getIcon(): string;

    public function getDate(): DateTimeInterface;

    /**
     * @return array{}|array{'link': string, 'label': string}
     */
    public function getButton(): array;

    public function getButtonLink(): ?string;

    public function getButtonLabel(): ?string;
}
