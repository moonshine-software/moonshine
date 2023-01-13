<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Middleware;

use function auth;

use Closure;

use function config;

use Illuminate\Http\Request;

use function redirect;
use function route;

class Authenticate
{
    public function handle($request, Closure $next)
    {
        if (auth(config('moonshine.auth.guard'))->guest() && ! $this->except($request)) {
            return redirect()->guest(route(config('moonshine.route.prefix').'.'.'login'));
        }

        return $next($request);
    }

    /**
     * Determine if the request has a URI that should pass through verification.
     *
     * @param  Request  $request
     *
     * @return bool
     */
    protected function except(Request $request): bool
    {
         $prefix = config('moonshine.route.prefix') ? config('moonshine.route.prefix').'/' : '';

        return $request->is([
            "{$prefix}login",
            "{$prefix}authenticate",
            "{$prefix}logout",
            "{$prefix}socialite/*",
        ]);
    }
}
