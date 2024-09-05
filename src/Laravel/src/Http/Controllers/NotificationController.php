<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class NotificationController extends MoonShineController
{
    public function readAll(): RedirectResponse
    {
        $this->notification->readAll();

        return back();
    }

    public function read(int|string $notification): RedirectResponse
    {
        $this->notification->markAsRead($notification);

        return back();
    }
}
