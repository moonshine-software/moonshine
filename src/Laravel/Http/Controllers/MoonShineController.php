<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use MoonShine\Core\Pages\ViewPage;
use MoonShine\Laravel\Http\Responses\MoonShineJsonResponse;
use MoonShine\Laravel\Pages\Page;
use MoonShine\Laravel\Traits\Controller\InteractsWithAuth;
use MoonShine\Laravel\Traits\Controller\InteractsWithUI;
use MoonShine\Support\Enums\ToastType;
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
                message: moonshine()->isProduction() ? __('moonshine::ui.saved_error') : $e->getMessage(),
                messageType: 'error'
            );
        }

        throw_if(! moonshine()->isProduction(), $e);

        report_if(moonshine()->isProduction(), $e);

        $this->toast(
            moonshine()->isProduction() ? __('moonshine::ui.saved_error') : $e->getMessage(),
            ToastType::ERROR
        );

        return redirect($redirectRoute)->withInput();
    }
}
