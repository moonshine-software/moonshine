<?php

namespace Leeto\MoonShine\Middleware;

use Closure;
use Illuminate\Http\Request;

class Authenticate
{
    public function handle($request, Closure $next)
    {
        if (auth(config('moonshine.auth.guard'))->guest() && !$this->except($request)) {
            return redirect()->guest(route(config('moonshine.route.prefix') . '.' . 'login'));
        }

        return $next($request);
    }

    /**
     * Determine if the request has a URI that should pass through verification.
     *
     * @param Request $request
     *
     * @return bool
     */
    protected function except(Request $request): bool
    {
        return $request->is([
            config('moonshine.route.prefix') . '/login',
            config('moonshine.route.prefix') . '/logout',
        ]);
    }
}
