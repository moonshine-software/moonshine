<?php

declare(strict_types=1);

namespace MoonShine\Traits\Controller;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use MoonShine\MoonShineAuth;

trait InteractsWithAuth
{
    public function auth(): Guard|StatefulGuard
    {
        return MoonShineAuth::guard();
    }
}
