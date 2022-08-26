<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Validation\ValidationException;
use Leeto\MoonShine\Http\Requests\Auth\LoginFormRequest;
use Leeto\MoonShine\Traits\Controllers\ApiResponder;
use Leeto\MoonShine\Http\Resources\MoonShineUserJsonResource;
use Illuminate\Http\JsonResponse;

class AuthController extends BaseController
{
    use ApiResponder;

    /**
     * @throws ValidationException
     */
    public function authenticate(LoginFormRequest $request): JsonResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return $this->jsonResponse([
            'user' => new MoonShineUserJsonResource(auth('moonshine')->user())
        ]);
    }

    public function logout(): Response
    {
        auth('moonshine')->logout();

        request()->session()->invalidate();

        request()->session()->regenerateToken();

        return response()->noContent();
    }

    public function check(): JsonResponse
    {
        return $this->jsonResponse([
            'user' => new MoonShineUserJsonResource(auth('moonshine')->user())
        ]);
    }
}
