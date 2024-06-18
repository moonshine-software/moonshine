<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Middleware;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use MoonShine\Laravel\MoonShineAuth;
use MoonShine\Laravel\Support\JWT;

class AuthenticateApi extends Middleware
{
    protected function authenticate($request, array $guards): void
    {
        if (! moonshineConfig()->isAuthEnabled()) {
            return;
        }

        $guard = MoonShineAuth::getGuard();

        $identity = (new JWT())->parse($request->bearerToken() ?? '');

        if ($identity === false) {
            $this->unauthenticated($request, [$guard, ...$guards]);
        }

        $guard->loginUsingId($identity);

        $this->auth->shouldUse(MoonShineAuth::getGuardName());
    }

    protected function unauthenticated($request, array $guards)
    {
        $request->headers->set('accept', 'application/json');

        throw new AuthenticationException(
            'Unauthenticated.',
            $guards,
        );
    }
}
