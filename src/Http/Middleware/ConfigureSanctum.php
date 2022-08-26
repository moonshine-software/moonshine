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
            config()->set('sanctum.stateful', config('moonshine.stateful'));

            config()->set('cors.path', [config('moonshine.prefix').'/*']);
            config()->set('cors.supports_credentials', true);
            config()->set('cors.allowed_origins', config('moonshine.frontend_url'));
        }

        return $next($request);
    }
}
