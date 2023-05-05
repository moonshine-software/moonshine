<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Validation\ValidationException;
use MoonShine\Http\Requests\LoginFormRequest;
use MoonShine\MoonShineAuth;

class AuthenticateController extends BaseController
{
    public function login(): View|RedirectResponse
    {
        if (MoonShineAuth::guard()->check()) {
            return to_route('moonshine.index');
        }

        return view('moonshine::auth.login');
    }

    /**
     * @throws ValidationException
     */
    public function authenticate(LoginFormRequest $request): RedirectResponse
    {
        $request->authenticate();

        return redirect()
            ->intended(route('moonshine.index'));
    }

    public function logout(): RedirectResponse
    {
        MoonShineAuth::guard()->logout();

        return to_route('moonshine.login');
    }
}
