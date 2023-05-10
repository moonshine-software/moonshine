<?php

declare(strict_types=1);

namespace MoonShine\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class ChangeLocale
{
    public const KEY = 'change-moonshine-locale';

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
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
