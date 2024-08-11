<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Controllers;

use MoonShine\Laravel\MoonShineRequest;
use MoonShine\Support\Enums\ToastType;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

final class MethodController extends MoonShineController
{
    /**
     * @throws Throwable
     */
    public function __invoke(MoonShineRequest $request): Response
    {
        $toast = [
            'type' => 'info',
            'message' => $request->input('message', ''),
        ];

        try {
            $pageOrResource = $request->hasResource()
                ? $request->getResource()
                : $request->getPage();

            $result = $pageOrResource
                ?->{$request->input('method')}(
                    $request
                );

            $toast = $request->session()->get('toast', $toast);
        } catch (Throwable $e) {
            report($e);

            $result = $e;
        }

        $request->session()->forget('toast');

        if ($result instanceof JsonResponse) {
            return $result;
        }

        if ($result instanceof BinaryFileResponse || $result instanceof StreamedResponse) {
            return $result;
        }

        return $this->json(
            message: $result instanceof Throwable ? $result->getMessage() : $toast['message'],
            redirect: $result instanceof RedirectResponse ? $result->getTargetUrl() : null,
            messageType: $result instanceof Throwable ? ToastType::ERROR : ToastType::from($toast['type'])
        );
    }
}
