<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Leeto\MoonShine\Menu\Menu;
use Leeto\MoonShine\Traits\Controllers\ApiResponder;

class InitialController extends BaseController
{
    use ApiResponder;

    public function __invoke(): JsonResponse
    {
        return response()->json([
            'menu' => [
                'items' => app(Menu::class)->all()
            ],
            'settings' => [],
            'user' => auth('moonshine')->user(),
        ]);
    }
}
