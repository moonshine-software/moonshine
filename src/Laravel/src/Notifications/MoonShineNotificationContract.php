<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Notifications;

use Illuminate\Support\Collection;
use MoonShine\Support\Enums\Color;

/**
 * @template-covariant I of NotificationItemContract
 */
interface MoonShineNotificationContract
{
    /**
     * @param  array{}|array{'link': string, 'label': string}  $button
     * @param  array<int|string>  $ids
     */
    public function notify(
        string $message,
        array $button = [],
        array $ids = [],
        string|Color|null $color = null
    ): void;

    /**
     * @return Collection<int, I>
     */
    public function getAll(): Collection;

    public function readAll(): void;

    public function markAsRead(int|string $id): void;

    public function getReadAllRoute(): string;
}
