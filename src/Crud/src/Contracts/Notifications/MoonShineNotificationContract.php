<?php

declare(strict_types=1);

namespace MoonShine\Crud\Contracts\Notifications;

use Illuminate\Support\Collection;
use MoonShine\Support\Enums\Color;

/**
 * @template I of NotificationItemContract = NotificationItemContract
 */
interface MoonShineNotificationContract
{
    /**
     * @param  array<int|string>  $ids
     */
    public function notify(
        string $message,
        ?NotificationButtonContract $button = null,
        array $ids = [],
        string|Color|null $color = null,
        ?string $icon = null
    ): void;

    /**
     * @return Collection<array-key, I>
     */
    public function getAll(): Collection;

    public function readAll(): void;

    public function markAsRead(int|string $id): void;

    public function getReadAllRoute(): string;
}
