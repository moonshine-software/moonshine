<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use MoonShine\Laravel\Exceptions\InvalidHome;
use MoonShine\Laravel\Pages\Dashboard;
use Throwable;

class HomeController extends MoonShineController
{
    /**
     * @throws Throwable
     * @throws InvalidHome
     */
    public function __invoke(): RedirectResponse|View|string
    {
        return moonshineConfig()
            ->getPage('dashboard', Dashboard::class)
            ->render();
    }
}
