<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Routing\Controller as BaseController;
use MoonShine\MoonShineAuth;

class NotificationController extends BaseController
{
    public function readAll(): RedirectResponse
    {
        MoonShineAuth::guard()->user()
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
