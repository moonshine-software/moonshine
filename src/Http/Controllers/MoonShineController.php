<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use MoonShine\Pages\Page;
use MoonShine\Pages\ViewPage;
use MoonShine\Traits\Controller\InteractsWithAuth;
use MoonShine\Traits\Controller\InteractsWithUI;
use Symfony\Component\HttpFoundation\JsonResponse;
use Throwable;

abstract class MoonShineController extends BaseController
{
    use InteractsWithUI;
    use InteractsWithAuth;

    protected function json(string $message, array $data = [], string $redirect = null): JsonResponse
    {
        $data = ['message' => $message, ...$data];

        if($redirect) {
            $data['redirect'] = $redirect;
        }

        return response()->json($data);
    }

    protected function view(string $path, array $data = []): Page
    {
        $page = ViewPage::make();

        $page->beforeRender();

        return $page->setContentView($path, $data);
    }

    protected function tryOrRedirect(callable $callable, string $redirectRoute): mixed
    {
        try {
            return $callable();
        } catch (Throwable $e) {
            throw_if(! app()->isProduction(), $e);
            report_if(app()->isProduction(), $e);

            $this->toast(
                __('moonshine::ui.saved_error'),
                'error'
            );

            return redirect($redirectRoute);
        }
    }
}
