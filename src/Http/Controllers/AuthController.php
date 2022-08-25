<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Leeto\MoonShine\Http\Requests\Auth\LoginFormRequest;
use Leeto\MoonShine\Models\MoonshineUser;
use Leeto\MoonShine\Traits\Controllers\ApiResponder;

class AuthController extends BaseController
{
    use ApiResponder;

    /**
     * @throws ValidationException
     */
    public function authenticate(LoginFormRequest $request)
    {
        $user = MoonshineUser::where('email', $request->get('email'))->first();

        if (!$user || !Hash::check($request->get('password'), $user->password)) {
            throw ValidationException::withMessages([
                'login' => trans('moonshine::auth.failed')
            ]);
        }

        return response()->json([
            'token' => $user->createToken('moonshine')->plainTextToken
        ]);
    }

    public function logout(): Response
    {
        auth('moonshine')->user()
            ->currentAccessToken()
            ->delete();

        return response()->noContent();
    }
}
