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
        ToastType $messageType = ToastType::SUCCESS
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
        report_if(moonshine()->isProduction(), $e);

        $message = moonshine()->isProduction() ? __('moonshine::ui.saved_error') : $e->getMessage();
        $type = ToastType::ERROR;

        if($flash = session()->get('toast')) {
            session()->forget(['toast', '_flash.old', '_flash.new']);

            $message = $flash['message'] ?? $message;
        }

        if ($isAjax) {
            return $this->json(message: $message, messageType: $type);
        }

        throw_if(! moonshine()->isProduction(), $e);

        $this->toast($message, $type);

        return redirect($redirectRoute)->withInput();
    }
}
