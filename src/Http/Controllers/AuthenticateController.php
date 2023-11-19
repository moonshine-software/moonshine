<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use MoonShine\Forms\LoginForm;
use MoonShine\Http\Requests\LoginFormRequest;

class AuthenticateController extends MoonShineController
{
    public function login(): View|RedirectResponse
    {
        if ($this->auth()->check()) {
            return to_route(
                moonShineIndexRoute()
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
        $request->authenticate();

        return redirect()->intended(
            route(
                moonShineIndexRoute()
            )
        );
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('moonshine.login');
    }
}
