<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class ChangeLocale
{
    final public const KEY = '_lang';

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->input(
            self::KEY,
            session(self::KEY, moonshineConfig()->getLocale())
        );

        $locale = strtolower((string) $locale);

        if (! \in_array($locale, moonshineConfig()->getLocales(), true)) {
            return $next($request);
        }

        if ($locale) {
            app()->setLocale($locale);
            moonshineConfig()->locale($locale);
        }

        if ($request->has(self::KEY)) {
            session()->put(self::KEY, $locale);
        }

        return $next($request);
    }
}
