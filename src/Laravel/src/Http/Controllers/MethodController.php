<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Controllers;

use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Leeto\FastAttributes\Attributes;
use MoonShine\Contracts\Core\DependencyInjection\CrudRequestContract;
use MoonShine\Support\Attributes\AsyncMethod;
use MoonShine\Support\Enums\ToastType;
use RuntimeException;
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
    public function __invoke(Request $request, CrudRequestContract $crudRequest, Container $container): Response
    {
        $toast = [
            'type' => 'info',
            'message' => $request->input('message', ''),
        ];

        try {
            $method = $request->input('method');
            $page = $crudRequest->getPage();
            $pageOrResource = $crudRequest->hasResource()
                ? $crudRequest->getResource()
                : $page;

            $target = method_exists($page, $method) ? $page : $pageOrResource;

            if (! Attributes::for($target, AsyncMethod::class)->method($method)->first() instanceof AsyncMethod) {
                throw new RuntimeException("$method does not exist");
            }

            $result = $container->call([$target, $method]);

            $toast = $request->session()->get('toast', $toast);
        } catch (Throwable $e) {
            report($e);

            $result = $e;
        }

        $request->session()->forget('toast');

        if ($result instanceof ValidationException) {
            return $this->json($result->getMessage(), data: [
                'message' => $result->getMessage(),
                'errors' => $result->errors(),
            ], messageType: ToastType::ERROR, status: $result->status);
        }

        if ($result instanceof JsonResponse) {
            return $result;
        }

        if ($result instanceof BinaryFileResponse || $result instanceof StreamedResponse) {
            return $result;
        }

        if (\is_string($result)) {
            return response($result);
        }

        return $this->json(
            message: $result instanceof Throwable ? $result->getMessage() : $toast['message'],
            redirect: $result instanceof RedirectResponse ? $result->getTargetUrl() : null,
            messageType: $result instanceof Throwable ? ToastType::ERROR : ToastType::from($toast['type']),
            status: $result instanceof Throwable ? Response::HTTP_INTERNAL_SERVER_ERROR : Response::HTTP_OK,
        );
    }
}
