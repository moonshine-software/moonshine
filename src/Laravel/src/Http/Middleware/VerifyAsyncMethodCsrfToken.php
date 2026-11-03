<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Middleware;

use Closure;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Symfony\Component\HttpFoundation\Response;

/**
 * Async methods may change state for any HTTP verb, but the framework's
 * request-forgery middleware exempts GET, HEAD and OPTIONS. For those verbs
 * this middleware runs the request-forgery middleware already configured for
 * the route once more, as if the request used an unsafe verb. Configured
 * exclusions, custom token checks and origin verification therefore apply
 * unchanged, and routes without request-forgery protection (for example a
 * session-less API setup) are left alone.
 */
final readonly class VerifyAsyncMethodCsrfToken
{
    private const array READ_METHODS = ['GET', 'HEAD', 'OPTIONS'];

    public function __construct(
        private Container $container,
        private Router $router,
    ) {
    }

    public function handle(Request $request, Closure $next): mixed
    {
        $route = $request->route();

        if (! $route instanceof Route || ! \in_array($request->getMethod(), self::READ_METHODS, true)) {
            return $next($request);
        }

        foreach ($this->getForgeryMiddleware($route) as $middleware) {
            $this->verify($middleware, $request);
        }

        return $next($request);
    }

    /**
     * @return list<class-string>
     */
    private function getForgeryMiddleware(Route $route): array
    {
        // PreventRequestForgery is the base class since Laravel 13, VerifyCsrfToken before it.
        $base = class_exists(PreventRequestForgery::class) ? PreventRequestForgery::class : VerifyCsrfToken::class;
        $middleware = [];

        foreach ($this->router->gatherRouteMiddleware($route) as $name) {
            if (! \is_string($name)) {
                continue;
            }

            $class = explode(':', $name, 2)[0];

            if (class_exists($class) && is_a($class, $base, true)) {
                $middleware[] = $class;
            }
        }

        return array_values(array_unique($middleware));
    }

    /**
     * @param  class-string  $middleware
     */
    private function verify(string $middleware, Request $request): void
    {
        // PUT is not subject to method overriding, unlike POST, so `_method` cannot turn the check back into a read.
        $verification = clone $request;
        $verification->setMethod('PUT');

        $instance = $this->container->make($middleware);

        if (! \is_object($instance) || ! method_exists($instance, 'handle')) {
            return;
        }

        $instance->handle($verification, static fn (): Response => new Response());
    }
}
