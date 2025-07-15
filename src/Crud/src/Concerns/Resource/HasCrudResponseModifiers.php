<?php

declare(strict_types=1);

namespace MoonShine\Crud\Concerns\Resource;

use MoonShine\Crud\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

trait HasCrudResponseModifiers
{
    public function modifyDestroyResponse(JsonResponse $response): JsonResponse
    {
        return $response;
    }

    public function modifyMassDeleteResponse(JsonResponse $response): JsonResponse
    {
        return $response;
    }

    public function modifySaveResponse(JsonResponse $response): JsonResponse
    {
        return $response;
    }

    public function modifyErrorResponse(Response $response, Throwable $exception): Response
    {
        return $response;
    }
}
