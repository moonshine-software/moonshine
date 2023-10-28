<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use MoonShine\Pages\Page;
use MoonShine\Pages\ViewPage;
use MoonShine\Traits\Controller\InteractsWithAuth;
use MoonShine\Traits\Controller\InteractsWithUI;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class MoonShineController extends BaseController
{
    use InteractsWithUI;
    use InteractsWithAuth;

    public function json(string $message, array $data = [], string $redirect = null): JsonResponse
    {
        $data = array_merge([
            'message' => $message
        ], $data);

        if($redirect) {
            $data['redirect'] = $redirect;
        }

        return response()->json($data);
    }

    public function view(string $path, array $data = []): Page
    {
        $page = ViewPage::make();

        $page->beforeRender();

        return $page->setContentView($path, $data);
    }
}
