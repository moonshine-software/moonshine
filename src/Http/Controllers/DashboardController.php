<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use Illuminate\Contracts\View\View;

class DashboardController extends MoonShineController
{
    public function __invoke(): View|string
    {
        /* @var Page $page */
        $page = new (config('moonshine.pages.dashboard'))();

        return $page->render();
    }
}
