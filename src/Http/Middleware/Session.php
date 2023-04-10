<?php

declare(strict_types=1);

namespace MoonShine\Http\Middleware;

use Closure;

use function config;

use Illuminate\Http\Request;

class Session
{
    public function handle(Request $request, Closure $next)
    {
        $path = config('moonshine.route.prefix')
            ? '/'.config('moonshine.route.prefix')
            : '/';

        config(['session.path' => $path]);

        if ($domain = config('moonshine.route.domain')) {
            config(['session.domain' => $domain]);
        }

        return $next($request);
    }
}
