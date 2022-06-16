<?php

namespace Leeto\MoonShine\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Routing\Redirector;

use function auth;
use function back;
use function redirect;
use function trans;
use function view;

class MoonShineAuthController extends BaseController
{
    public function login(Request $request): Factory|View|Redirector|Application|RedirectResponse
    {
        if (auth(config('moonshine.auth.guard'))->check()) {
            return redirect(route(config('moonshine.route.prefix') . '.index'));
        }

        if ($request->isMethod('post')) {
            $credentials = $request->only(['email', 'password']);
            $remember = $request->get('remember', false);

            if (auth(config('moonshine.auth.guard'))->attempt($credentials, $remember)) {
                return redirect(url()->previous());
            } else {
                $request->session()->flash('alert', trans('moonshine::auth.failed'));

                return back()
                    ->withInput()
                    ->withErrors(['login' => trans('moonshine::auth.failed')]);
            }
        }

        return view('moonshine::auth.login');
    }

    public function logout(): Redirector|Application|RedirectResponse
    {
        auth(config('moonshine.auth.guard'))->logout();

        return redirect(route(config('moonshine.route.prefix') . '.login'));
    }
}
