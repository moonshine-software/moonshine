<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

final class VerifyAsyncMethodCsrfToken extends VerifyCsrfToken
{
    protected function isReading($request): bool
    {
        // Async methods may change state for any HTTP verb.
        return false;
    }
}
