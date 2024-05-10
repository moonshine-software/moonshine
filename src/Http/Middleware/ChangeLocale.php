<?php

declare(strict_types=1);

namespace MoonShine\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ChangeLocale
{
    final public const KEY = '_lang';

    public function handle(Request $request, Closure $next)
    {
        $local = $request->get(
            self::KEY,
            session(self::KEY)
        );

        if ($local) {
            app()->setLocale($local);
            session()->put(self::KEY, $local);
        }

        return $next($request);
    }
}
