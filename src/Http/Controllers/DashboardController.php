<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller as BaseController;

class DashboardController extends BaseController
{
    public function __invoke(): View
    {
        /* @var Page $page */
        $page = config('moonshine.pages.dashboard');

        return $page::make()->render();
    }
}
