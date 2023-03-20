<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class ChangeLocale
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function handle(Request $request, Closure $next)
    {
        $key = 'change-moonshine-locale';
        $local = $request->get(
            $key,
            cache()->get($key)
        );

        if ($local) {
            app()->setLocale($local);
            cache()->rememberForever($key, fn() => $local);
        }

        return $next($request);
    }
}
