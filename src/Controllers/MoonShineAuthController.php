<?php

namespace Leeto\MoonShine\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MoonShineAuthController extends BaseController
{
    public function login(Request $request): Factory|View|Redirector|Application|RedirectResponse
    {
        if (auth(config('moonshine.auth.guard'))->check()) {
            return redirect(route(config("moonshine.route.prefix") . '.index'));
        }

        if ($request->isMethod('post')) {
            $credentials = $request->only(['email', 'password']);
            $remember = $request->get('remember', false);

            if (auth(config('moonshine.auth.guard'))->attempt($credentials, $remember)) {
                return redirect(url()->previous());
            } else {
                $request->session()->flash('alert', trans('moonshine::ui.login.notfound'));

                return back()
                    ->withInput()
                    ->withErrors(['login' => trans('moonshine::ui.login.notattempt')]);
            }
        }

        return view('moonshine::index.login');
    }

    public function logout(): Redirector|Application|RedirectResponse
    {
        auth(config('moonshine.auth.guard'))->logout();

        return redirect(route(config("moonshine.route.prefix") . '.login'));
    }

    public function attachments(Request $request): array
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            return [
                'attachment' => Storage::url($file->store('attachments', 'public'))
            ];
        }

        return [];
    }
}
