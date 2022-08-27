<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

final class ConfigureSanctum
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->routeIs(config('moonshine.prefix').'.*')) {
            config()->set('sanctum.guard', ['moonshine']);
            config()->set('sanctum.stateful', config('moonshine.stateful'));
        }

        return $next($request);
    }
}
