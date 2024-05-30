<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Resource;

use Illuminate\Support\Facades\Gate;
use MoonShine\Core\Exceptions\ResourceException;
use MoonShine\Laravel\MoonShineAuth;

trait ResourceModelPolicy
{
    protected bool $withPolicy = false;

    /**
     * @return string[]
     */
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

    /**
     * @throws ResourceException
     */
    public function can(string $ability): bool
    {
        if (! moonshineConfig()->isAuthEnabled()) {
            return true;
        }

        if (! in_array($ability, $this->gateAbilities())) {
            throw new ResourceException("ability '$ability' not found in the system");
        }

        $user = MoonShineAuth::guard()->user();

        $checkCustomRules = moonshineConfig()
            ->getAuthorizationRules()
            ->every(fn ($rule) => $rule($this, $user, $ability, $this->getItem() ?? $this->getModel()));

        if (! $checkCustomRules) {
            return false;
        }

        if (! $this->isWithPolicy()) {
            return true;
        }

        return Gate::forUser($user)
            ->allows($ability, $this->getItem() ?? $this->getModel());
    }

    public function isWithPolicy(): bool
    {
        return $this->withPolicy;
    }
}
