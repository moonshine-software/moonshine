<?php

declare(strict_types=1);

namespace MoonShine\Http\Middleware;

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
            session($key)
        );

        if ($local) {
            app()->setLocale($local);
            session()->put($key, $local);
        }

        return $next($request);
    }
}
