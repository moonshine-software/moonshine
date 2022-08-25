<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ConfigureSanctum
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->routeIs(config('moonshine.prefix').'.*')) {
            config()->set('sanctum.guard', ['moonshine']);
            //todo: возможно стоит сдлеать как в breeze frontend_url и stub который меняет sanctum конфиг
            //config()->set('sanctum.stateful', .......);

            // Этого не нужно. Иначе при каждом коннекте он устанавливает новую куку. Для авторизации они не нужны
            //config()->set('session.driver', 'cookie');
            //config()->set('session.domain', $request->host());

            config()->set('cors.path', [config('moonshine.prefix').'/*']);
            config()->set('cors.cors.supports_credentials', true);
            //
            config()->set('cors.allowed_origins', config('moonshine.frontend_url'));
        }

        return $next($request);
    }
}
