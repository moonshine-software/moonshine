<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Controller;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use MoonShine\Laravel\MoonShineAuth;

trait InteractsWithAuth
{
    protected function auth(): Guard|StatefulGuard
    {
        return MoonShineAuth::getGuard();
    }
}
