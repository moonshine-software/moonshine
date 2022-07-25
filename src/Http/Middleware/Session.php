<?php
declare(strict_types=1);

namespace Leeto\MoonShine\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use function config;

class Session
{
	public function handle(Request $request, Closure $next)
	{
		$path = '/' . trim(config('moonshine.route.prefix'), '/');

		config(['session.path' => $path]);

		if ($domain = config('moonshine.route.domain')) {
			config(['session.domain' => $domain]);
		}

		return $next($request);
	}
}
