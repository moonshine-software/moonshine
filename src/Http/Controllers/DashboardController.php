<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller as BaseController;

use Leeto\MoonShine\Dashboard\Dashboard;

class DashboardController extends BaseController
{
    public function __invoke(): View
    {
        return view('moonshine::dashboard', [
            'blocks' => app(Dashboard::class)->getBlocks(),
        ]);
    }
}
