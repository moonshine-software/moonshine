<?php

declare(strict_types=1);

namespace MoonShine\Crud\Contracts\Resource;

use MoonShine\Crud\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

interface HasCrudResponseModifiersContract
{
    public function modifyDestroyResponse(JsonResponse $response): JsonResponse;

    public function modifyMassDeleteResponse(JsonResponse $response): JsonResponse;

    public function modifySaveResponse(JsonResponse $response): JsonResponse;

    public function modifyErrorResponse(Response $response, Throwable $exception): Response;
}
