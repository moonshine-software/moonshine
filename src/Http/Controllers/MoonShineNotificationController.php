<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Routing\Controller as BaseController;

class MoonShineNotificationController extends BaseController
{
    public function readAll(): RedirectResponse
    {
        auth(config('moonshine.auth.guard'))->user()
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
