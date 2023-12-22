<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use MoonShine\Exceptions\InvalidHome;

class HomeController extends MoonShineController
{
    /**
     * @throws \Throwable
     * @throws InvalidHome
     */
    public function __invoke(): RedirectResponse|View|string
    {
        if ($url = moonshine()->homeUrl()) {
            return redirect($url);
        }

        /* @var \MoonShine\Pages\Page $page */
        $page = new (config('moonshine.pages.dashboard'))();

        return $page->render();
    }
}
