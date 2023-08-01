<?php

declare(strict_types=1);

namespace MoonShine\Notifications;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Facades\Notification;
use MoonShine\MoonShineAuth;

final class MoonShineNotification
{
    /**
     * @param  array{'link': string, 'label': string}  $button
     * @param  array<int>  $ids
     */
    public static function send(
        string $message,
        array $button = [],
        array $ids = [],
        string $color = 'green'
    ): void {
        if (config('moonshine.use_notifications', true)) {
            Notification::sendNow(
                MoonShineAuth::model()->query()
                    ->when(
                        $ids,
                        fn ($query): Builder => $query->whereIn(
                            MoonShineAuth::model()?->getKeyName() ?? 'id',
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
