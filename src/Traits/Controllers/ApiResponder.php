<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use JsonSerializable;

trait ApiResponder
{
    public function jsonResponse(JsonSerializable|array $data, int $status = Response::HTTP_OK): JsonResponse
    {
        return response()->json($data);
    }

    public function jsonSuccessMessage(string $message, int $status = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message
        ], $status);
    }

    public function jsonErrorMessage(string $message, int $status = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => $message
        ], $status);
    }
}
