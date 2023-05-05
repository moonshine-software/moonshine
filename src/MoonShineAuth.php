<?php

declare(strict_types=1);

namespace MoonShine;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

final class MoonShineAuth
{
    public static function guard(): Guard|StatefulGuard
    {
        return Auth::guard(config('moonshine.auth.guard'));
    }

    public static function provider(): ?UserProvider
    {
        return self::guard()?->getProvider();
    }

    public static function model(): ?Model
    {
        $model = self::provider()?->getModel();

        return $model ? new $model() : null;
    }
}
