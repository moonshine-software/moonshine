<?php

declare(strict_types=1);

namespace MoonShine\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use MoonShine\MoonShineAuth;

class Authenticate extends Middleware
{
    protected function authenticate($request, array $guards): void
    {
        if (! config('moonshine.auth.enable', true)) {
            return;
        }

        $guard = MoonShineAuth::guard();

        if (! $guard->check()) {
            $this->unauthenticated($request, $guards);
        }

        $this->auth->shouldUse(MoonShineAuth::guardName());
    }

    protected function redirectTo($request): string
    {
        return route('moonshine.login');
    }
}
