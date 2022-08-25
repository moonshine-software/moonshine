<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Validation\ValidationException;
use Leeto\MoonShine\Http\Requests\Auth\LoginFormRequest;
use Leeto\MoonShine\Traits\Controllers\ApiResponder;

class AuthController extends BaseController
{
    use ApiResponder;

    /**
     * @throws ValidationException
     */
    public function authenticate(LoginFormRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        return response()->json([
            'user' => auth('moonshine')->user()
        ]);
    }

    public function logout(): Response
    {
        auth('moonshine')->logout();

        request()->session()->invalidate();

        request()->session()->regenerateToken();

        return response()->noContent();
    }
}
