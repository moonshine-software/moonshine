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
use MoonShine\Pages\LoginPage;

class AuthenticateController extends MoonShineController
{
    public function login(): View|RedirectResponse|string
    {
        if ($this->auth()->check()) {
            return redirect(
                moonshineRouter()->home()
            );
        }

        return moonshine()
            ->getPageFromConfig('login', LoginPage::class)
            ->render();
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
            moonshineRouter()->home()
        );
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(
            moonshineRouter()->to('login')
        );
    }
}
