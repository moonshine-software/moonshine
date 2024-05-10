<?php

declare(strict_types=1);

namespace MoonShine\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use MoonShine\MoonShineAuth;

class Authenticate extends Middleware
{
    protected function authenticate($request, array $guards): void
    {
        if (! moonshineConfig()->isAuthEnabled()) {
            return;
        }

        $guard = MoonShineAuth::guard();

        if (! $guard->check()) {
            $this->unauthenticated($request, [$guard, ...$guards]);
        }

        $this->auth->shouldUse(MoonShineAuth::guardName());

    }

    protected function redirectTo($request): string
    {
        return moonshineRouter()->to('login');
    }
}
