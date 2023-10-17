<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use MoonShine\MoonShine;
use MoonShine\Pages\Page;

class HomeController extends MoonShineController
{
    public function __invoke(): RedirectResponse|View|string
    {
        if ($url = MoonShine::homeUrl()) {
            return redirect($url);
        }

        /* @var Page $page */
        $page = new (config('moonshine.pages.dashboard'))();

        return $page->render();
    }
}
