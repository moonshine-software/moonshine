<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Leeto\MoonShine\Http\Resources\MoonShineUserJsonResource;
use Leeto\MoonShine\Menu\Menu;
use Leeto\MoonShine\Traits\Controllers\ApiResponder;

class InitialController extends BaseController
{
    use ApiResponder;

    public function __invoke(): JsonResponse
    {
        $data = [
            'app' => [
                'name' => config('app.name')
            ],
            'settings' => [],
            'theme' => []
        ];

        if(! is_null(auth('moonshine')->user())) {
            $data['menu'] = ['items' => app(Menu::class)->all()];
        }

        return $this->jsonResponse($data);
    }
}
