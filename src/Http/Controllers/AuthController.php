<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Routing\Redirector;
use Leeto\MoonShine\Http\Requests\Auth\LoginFormRequest;

use function auth;
use function back;
use function redirect;
use function trans;
use function view;

class AuthController extends BaseController
{
    public function login(): Factory|View|Redirector|Application|RedirectResponse
    {
        if (auth(config('moonshine.auth.guard'))->check()) {
            return redirect(route(config('moonshine.route.prefix').'.index'));
        }

        return view('moonshine::auth.login');
    }

    public function authenticate(LoginFormRequest $request): RedirectResponse
    {
        $credentials = $request->only(['email', 'password']);
        $remember = $request->boolean('remember');

        if (auth(config('moonshine.auth.guard'))->attempt($credentials, $remember)) {
            return redirect(url()->previous());
        } else {
            $request->session()->flash('alert', trans('moonshine::auth.failed'));

            return back()
                ->withInput()
                ->withErrors(['login' => trans('moonshine::auth.failed')]);
        }
    }

    public function logout(): Redirector|Application|RedirectResponse
    {
        auth(config('moonshine.auth.guard'))->logout();

        return redirect(route(config('moonshine.route.prefix').'.login'));
    }
}
