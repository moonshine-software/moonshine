<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Leeto\MoonShine\Dashboard\Dashboard;
use Leeto\MoonShine\Traits\Controllers\ApiResponder;

class DashboardController extends BaseController
{
    use ApiResponder;

    public function __invoke(): JsonResponse
    {
        return response()->json([
            'blocks' => app(Dashboard::class)->getBlocks()
        ]);
    }
}
