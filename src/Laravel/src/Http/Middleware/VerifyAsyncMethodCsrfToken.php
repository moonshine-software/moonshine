<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use ReflectionProperty;

/**
 * Async methods may change state for any HTTP verb, so this middleware also
 * verifies GET, HEAD and OPTIONS requests. It applies only when the route is
 * already protected by a request-forgery middleware (for example, not in a
 * session-less API setup) and reuses that middleware's exclusions.
 */
final class VerifyAsyncMethodCsrfToken extends VerifyCsrfToken
{
    public function handle($request, Closure $next)
    {
        $route = $request->route();
        $configured = $route instanceof Route ? $this->getConfiguredMiddleware($route) : [];

        if ($configured === []) {
            return $next($request);
        }

        $this->except = array_values(array_unique(array_merge(
            ...array_map($this->getExcludedPathsOf(...), $configured),
        )));

        return parent::handle($request, $next);
    }

    protected function isReading($request): bool
    {
        // Async methods may change state for any HTTP verb.
        return false;
    }

    /**
     * @return list<VerifyCsrfToken>
     */
    private function getConfiguredMiddleware(Route $route): array
    {
        $middleware = [];

        foreach ($this->app->make(Router::class)->gatherRouteMiddleware($route) as $name) {
            if (! \is_string($name)) {
                continue;
            }

            $class = explode(':', $name, 2)[0];

            if ($class === self::class || ! class_exists($class) || ! is_a($class, VerifyCsrfToken::class, true)) {
                continue;
            }

            $instance = $this->app->make($class);

            // A configured class may be rebound to another implementation, e.g. to disable verification.
            if ($instance instanceof VerifyCsrfToken) {
                $middleware[] = $instance;
            }
        }

        return $middleware;
    }

    /**
     * @return array<array-key, string>
     */
    private function getExcludedPathsOf(VerifyCsrfToken $middleware): array
    {
        // getExcludedPaths() is not available on Laravel 10.
        if (method_exists($middleware, 'getExcludedPaths')) { // @phpstan-ignore function.alreadyNarrowedType
            /** @var array<array-key, string> */
            return $middleware->getExcludedPaths();
        }

        /** @var array<array-key, string> */
        return (new ReflectionProperty($middleware, 'except'))->getValue($middleware);
    }
}
