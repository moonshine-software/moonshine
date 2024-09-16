<?php

declare(strict_types=1);

namespace MoonShine\Laravel;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

final class MoonShineAuth
{
    public static function getModel(): ?Model
    {
        $model = self::getProvider()?->getModel();

        return $model ? new $model() : null;
    }

    /**
     * @return ?EloquentUserProvider
     */
    public static function getProvider(): ?UserProvider
    {
        /** @phpstan-ignore-next-line  */
        return self::getGuard()->getProvider();
    }

    public static function getGuard(?string $guard = null): Guard|StatefulGuard
    {
        return Auth::guard($guard ?? self::getGuardName());
    }

    public static function getGuardName(): string
    {
        return moonshineConfig()->getGuard();
    }
}
