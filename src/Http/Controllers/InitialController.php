<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Leeto\MoonShine\Menu\Menu;
use Leeto\MoonShine\Traits\Controllers\ApiResponder;

final class InitialController extends BaseController
{
    use ApiResponder;

    public function __invoke(): JsonResponse
    {
        $response = [
            'app' => [
                'name' => config('app.name')
            ],
            'theme' => [],
            'settings' => [],
        ];

        if (auth('moonshine')->check()) {
            $response['menu'] = [
                'items' => app(Menu::class)->all()
            ];
        }

        return $this->jsonResponse($response);
    }
}
