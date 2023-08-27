<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends MoonShineController
{
    public function readAll(): RedirectResponse
    {
        $this->auth()->user()
            ->unreadNotifications
            ->markAsRead();

        return back();
    }

    public function read(DatabaseNotification $notification): RedirectResponse
    {
        $notification->markAsRead();

        return back();
    }
}
