<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Notifications;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;
use MoonShine\Laravel\MoonShineAuth;
use MoonShine\Support\Enums\Color;

/**
 * @implements MoonShineNotification<NotificationItem>
 */
final class MoonShineNotification implements MoonShineNotificationContract
{
    /**
     * @param  array{}|array{'link': string, 'label': string}  $button
     * @param  array<int|string>  $ids
     */
    public static function send(
        string $message,
        array $button = [],
        array $ids = [],
        string|Color|null $color = null
    ): void {
        app(MoonShineNotificationContract::class)->notify($message, $button, $ids, $color);
    }

    /**
     * @param  array{}|array{'link': string, 'label': string}  $button
     * @param  array<int|string>  $ids
     */
    public function notify(
        string $message,
        array $button = [],
        array $ids = [],
        string|Color|null $color = null
    ): void {
        if (!moonshineConfig()->isUseNotifications()) {
            return;
        }

        $color = $color instanceof Color ? $color->value : $color;

        Notification::sendNow(
            MoonShineAuth::getModel()?->query()
                ->when(
                    $ids,
                    static fn ($query): Builder => $query->whereIn(
                        MoonShineAuth::getModel()?->getKeyName() ?? 'id',
                        $ids
                    )
                )
                ->get(),
            DatabaseNotification::make(
                $message,
                $button,
                $color
            )
        );
    }

    private function getUnreadNotifications(): DatabaseNotificationCollection
    {
        return MoonShineAuth::getGuard()->user()?->unreadNotifications ?? DatabaseNotificationCollection::make();
    }

    /**
     * @return Collection<int, NotificationItem>
     */
    public function getAll(): Collection
    {
        return $this->getUnreadNotifications()->mapInto(NotificationItem::class);
    }

    public function readAll(): void
    {
        $this->getUnreadNotifications()->markAsRead();
    }

    public function markAsRead(int|string $id): void
    {
        $this->getUnreadNotifications()->where('id', $id)->markAsRead();
    }

    public function getReadAllRoute(): string
    {
        return route('moonshine.notifications.readAll');
    }
}
