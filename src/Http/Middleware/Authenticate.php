<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Authenticate
{
    public function handle($request, Closure $next)
    {
        if (auth('moonshine')->guest() && !$this->except($request)) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
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
        return $request->is([
            config('moonshine.prefix').'/authenticate',
            config('moonshine.prefix').'/logout',
        ]);
    }
}
