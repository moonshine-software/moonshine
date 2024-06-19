<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Notifications;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Facades\Notification;
use MoonShine\Laravel\MoonShineAuth;

final class MoonShineNotification
{
    /**
     * @param  array{}|array{'link': string, 'label': string}  $button
     * @param  array<int|string>  $ids
     */
    public static function send(
        string $message,
        array $button = [],
        array $ids = [],
        ?string $color = null
    ): void {
        if (moonshineConfig()->isUseNotifications()) {
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
                MoonShineDatabaseNotification::make(
                    $message,
                    $button,
                    $color
                )
            );
        }
    }
}
