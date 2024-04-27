<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use MoonShine\Enums\ToastType;
use MoonShine\Http\Responses\MoonShineJsonResponse;
use MoonShine\Pages\Page;
use MoonShine\Pages\ViewPage;
use MoonShine\Traits\Controller\InteractsWithAuth;
use MoonShine\Traits\Controller\InteractsWithUI;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

abstract class MoonShineController extends BaseController
{
    use InteractsWithUI;
    use InteractsWithAuth;

    protected function json(
        string $message = '',
        array $data = [],
        string $redirect = null,
        string|ToastType $messageType = 'success'
    ): JsonResponse {
        return MoonShineJsonResponse::make(data: $data)
            ->toast($message, $messageType)
            ->when(
                $redirect,
                fn (MoonShineJsonResponse $response): MoonShineJsonResponse => $response->redirect($redirect)
            );
    }

    protected function view(string $path, array $data = []): Page
    {
        return ViewPage::make()->setContentView($path, $data);
    }

    /**
     * @throws Throwable
     */
    protected function reportAndResponse(bool $isAjax, Throwable $e, string $redirectRoute): Response
    {
        if ($isAjax) {
            report($e);

            return $this->json(
                message: app()->isProduction() ? __('moonshine::ui.saved_error') : $e->getMessage(),
                messageType: 'error'
            );
        }

        throw_if(! app()->isProduction(), $e);

        report_if(app()->isProduction(), $e);

        $this->toast(
            app()->isProduction() ? __('moonshine::ui.saved_error') : $e->getMessage(),
            'error'
        );

        return redirect($redirectRoute)->withInput();
    }
}
