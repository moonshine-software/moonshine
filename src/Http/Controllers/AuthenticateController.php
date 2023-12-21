<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Validation\ValidationException;
use MoonShine\Forms\LoginForm;
use MoonShine\Http\Requests\LoginFormRequest;

class AuthenticateController extends MoonShineController
{
    public function login(): View|RedirectResponse
    {
        if ($this->auth()->check()) {
            return to_route(
                moonshineIndexRoute()
            );
        }

        $form = config('moonshine.forms.login', LoginForm::class);

        return view('moonshine::auth.login', [
            'form' => new $form(),
        ]);
    }

    /**
     * @throws ValidationException
     */
    public function authenticate(LoginFormRequest $request): RedirectResponse
    {
        if (filled(config('moonshine.auth.pipelines', []))) {
            $request = (new Pipeline(app()))->send($request)->through(array_filter(
                config('moonshine.auth.pipelines')
            ))->thenReturn();
        }

        if($request instanceof RedirectResponse) {
            return $request;
        }

        $request->authenticate();

        return redirect()->intended(
            route(
                moonshineIndexRoute()
            )
        );
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('moonshine.');
    }
}
