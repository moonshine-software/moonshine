<?php

declare(strict_types=1);

namespace MoonShine\Traits\Resource;

use Illuminate\Support\Facades\Gate;
use MoonShine\MoonShineAuth;

trait ResourceModelPolicy
{
    public static bool $withPolicy = false;

    public function isWithPolicy(): bool
    {
        return static::$withPolicy;
    }

    public function gateAbilities(): array
    {
        return [
            'viewAny',
            'view',
            'create',
            'update',
            'delete',
            'massDelete',
            'restore',
            'forceDelete',
        ];
    }

    public function can(string $ability): bool
    {
        $user = MoonShineAuth::guard()->user();

        if ($user->moonshineUserPermission
            && (! $user->moonshineUserPermission->permissions->has(get_class($this))
                || ! isset($user->moonshineUserPermission->permissions[get_class($this)][$ability]))) {
            return false;
        }

        if (! $this->isWithPolicy()) {
            return true;
        }

        return Gate::forUser($user)
            ->allows($ability, $this->getItem() ?? $this->getModel());
    }
}
