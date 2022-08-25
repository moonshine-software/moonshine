<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ConfigureSanctum
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->routeIs(config('moonshine.prefix').'.*')) {
            config()->set('sanctum.guard', ['moonshine']);
            //config()->set('sanctum.stateful', $request->host());

            config()->set('session.driver', 'cookie');
            //config()->set('session.domain', $request->host());

            config()->set('cors.path', [config('moonshine.prefix').'/*']);
            config()->set('cors.cors.supports_credentials', true);
        }

        return $next($request);
    }
}
