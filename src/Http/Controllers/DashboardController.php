<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller as BaseController;

use Leeto\MoonShine\Dashboard\Dashboard;

use function view;

class DashboardController extends BaseController
{
    public function __invoke(): Factory|View|Application
    {
        return view('moonshine::dashboard', [
            'blocks' => app(Dashboard::class)->getBlocks(),
        ]);
    }
}
