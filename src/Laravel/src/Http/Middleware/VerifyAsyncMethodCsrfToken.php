<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Middleware;

use Closure;
use Illuminate\Contracts\Http\Kernel as KernelContract;
use Illuminate\Foundation\Http\Kernel;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use ReflectionProperty;

/**
 * Async methods may change state for any HTTP verb, so this middleware also
 * verifies GET, HEAD and OPTIONS requests. It applies only when the route or
 * the global middleware stack already has a request-forgery middleware (for
 * example, not in a session-less API setup) and reuses its exclusions.
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
        $router = $this->app->make(Router::class);
        $middleware = [];

        foreach ([...$router->resolveMiddleware($this->getGlobalMiddleware()), ...$router->gatherRouteMiddleware($route)] as $name) {
            if (! \is_string($name)) {
                continue;
            }

            $class = explode(':', $name, 2)[0];

            if ($class === self::class || isset($middleware[$class]) || ! class_exists($class) || ! is_a($class, VerifyCsrfToken::class, true)) {
                continue;
            }

            $instance = $this->app->make($class);

            // A configured class may be rebound to another implementation, e.g. to disable verification.
            if ($instance instanceof VerifyCsrfToken) {
                $middleware[$class] = $instance;
            }
        }

        return array_values($middleware);
    }

    /**
     * Request-forgery protection may also be registered in the global HTTP middleware stack.
     *
     * @return array<array-key, mixed>
     */
    private function getGlobalMiddleware(): array
    {
        $kernel = $this->app->make(KernelContract::class);

        if (! $kernel instanceof Kernel) {
            return [];
        }

        // getGlobalMiddleware() is not available on Laravel 10.
        if (method_exists($kernel, 'getGlobalMiddleware')) { // @phpstan-ignore function.alreadyNarrowedType
            return $kernel->getGlobalMiddleware();
        }

        $middleware = (new ReflectionProperty($kernel, 'middleware'))->getValue($kernel);

        return \is_array($middleware) ? $middleware : [];
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
