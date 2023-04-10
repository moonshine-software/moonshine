<?php

declare(strict_types=1);

namespace MoonShine\Traits\Resource;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

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

    public function can(string $ability, Model $item = null): bool
    {
        $user = auth(config('moonshine.auth.guard'))->user();

        if ($user->moonshineUserPermission
            && (! $user->moonshineUserPermission->permissions->has(get_class($this))
                || ! isset($user->moonshineUserPermission->permissions[get_class($this)][$ability]))) {
            return false;
        }

        if (! $this->isWithPolicy()) {
            return true;
        }

        return Gate::forUser($user)
            ->allows($ability, $item ?? $this->getModel());
    }
}
