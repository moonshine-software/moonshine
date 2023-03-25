<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Controllers;

use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Routing\Redirector;
use Leeto\MoonShine\Http\Requests\LoginFormRequest;

class AuthenticateController extends BaseController
{
    public function login(): Factory|View|Redirector|Application|RedirectResponse
    {
        if (auth(config('moonshine.auth.guard'))->check()) {
            return redirect(route('moonshine.index'));
        }

        return view('moonshine::auth.login');
    }

    /**
     * @throws ValidationException
     */
    public function authenticate(LoginFormRequest $request): RedirectResponse
    {
        $request->authenticate();

        return redirect(url()->previous());
    }

    public function logout(): Redirector|Application|RedirectResponse
    {
        auth(config('moonshine.auth.guard'))->logout();

        return redirect(route('moonshine.login'));
    }
}
