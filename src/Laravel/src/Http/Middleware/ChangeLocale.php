<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ChangeLocale
{
    final public const KEY = '_lang';

    public function handle(Request $request, Closure $next): Response
    {
        $local = $request->input(
            self::KEY,
            session(self::KEY)
        );

        if ($local) {
            app()->setLocale($local);
            moonshineConfig()->locale($local);
            session()->put(self::KEY, $local);
        }

        return $next($request);
    }
}
