<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller as BaseController;
use MoonShine\Pages\Page;

class DashboardController extends BaseController
{
    public function __invoke(): View
    {
        /* @var Page $page */
        $page = new (config('moonshine.pages.dashboard'))();

        return $page->render();
    }
}
