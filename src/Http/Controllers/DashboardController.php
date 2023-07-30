<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller as BaseController;
use MoonShine\Dashboard\Dashboard;

class DashboardController extends BaseController
{
    public function __invoke(): View
    {
        return view('moonshine::dashboard', [
            'components' => app(Dashboard::class)->getComponents(),
        ]);
    }
}
