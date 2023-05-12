<?php

declare(strict_types=1);

namespace MoonShine\Traits\Resource;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use MoonShine\MoonShineAuth;
use MoonShine\Traits\Models\HasMoonShinePermissions;

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
        if (! config('moonshine.auth.enable', true)) {
            return true;
        }

        $user = MoonShineAuth::guard()->user();

        if (! $this->checkUserPermissions($user, $ability)) {
            return false;
        }

        if (! $this->isWithPolicy()) {
            return true;
        }

        return Gate::forUser($user)
            ->allows($ability, $this->getItem() ?? $this->getModel());
    }

    private function hasUserPermissions(): bool
    {
        return in_array(
            HasMoonShinePermissions::class,
            class_uses_recursive(MoonShineAuth::model()::class),
            true
        );
    }

    public function checkUserPermissions(Model $user, string $ability): bool
    {
        if (! $this->hasUserPermissions()) {
            return true;
        }

        if (! $user->moonshineUserPermission) {
            return true;
        }

        if (! isset($user->moonshineUserPermission->permissions[get_class($this)][$ability])) {
            return false;
        }

        return true;
    }
}
