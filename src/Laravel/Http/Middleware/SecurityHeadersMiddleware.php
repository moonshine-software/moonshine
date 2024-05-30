<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    protected array $removeHeaders = [
        'X-Powered-By',
        'x-powered-by',
        'Server',
        'server',
    ];

    protected array $addHeaders = [
        'Referrer-Policy' => 'no-referrer-when-downgrade',
        'X-Content-Type-Options' => 'nosniff',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        foreach ($this->removeHeaders as $header) {
            $response->headers->remove($header);
        }

        foreach ($this->addHeaders as $key => $value) {
            $response->headers->set(
                key: $key,
                values: $value,
            );
        }

        return $response;
    }
}
