<?php

namespace Leeto\MoonShine\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use JetBrains\PhpStorm\ArrayShape;

class IndexController extends Controller
{
    public function index(): Factory|View|Application
    {
        return view('moonshine::index.index');
    }

    public function login(Request $request): Factory|View|Redirector|Application|RedirectResponse
    {
        if (auth('moonshine')->check()) {
            return redirect(route('moonshine.index'));
        }

        if ($request->isMethod('post')) {
            $credentials = $request->only(['email', 'password']);
            $remember = $request->get('remember', false);

            if (auth('moonshine')->attempt($credentials, $remember)) {
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
        auth('moonshine')->logout();

        return redirect(route('moonshine.login'));
    }

    #[ArrayShape(['attachment' => "string"])]
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
