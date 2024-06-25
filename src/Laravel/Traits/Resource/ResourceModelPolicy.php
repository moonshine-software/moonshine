<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Resource;

use Illuminate\Support\Facades\Gate;
use MoonShine\Core\Exceptions\ResourceException;
use MoonShine\Laravel\Enums\Ability;
use MoonShine\Laravel\Enums\Action;
use MoonShine\Laravel\MoonShineAuth;

trait ResourceModelPolicy
{
    protected bool $withPolicy = false;

    /**
     * @return list<Ability>
     */
    public function getGateAbilities(): array
    {
        return [
            Ability::VIEW_ANY,
            Ability::VIEW,
            Ability::CREATE,
            Ability::UPDATE,
            Ability::DELETE,
            Ability::MASS_DELETE,
            Ability::RESTORE,
            Ability::FORCE_DELETE,
        ];
    }

    /**
     * @throws ResourceException
     */
    public function can(string|Ability $ability): bool
    {
        $abilityEnum = !$ability instanceof Ability ? Ability::tryFrom($ability) : $ability;

        if (! moonshineConfig()->isAuthEnabled()) {
            return true;
        }

        if (is_null($abilityEnum) || ! in_array($abilityEnum, $this->getGateAbilities(), true)) {
            throw new ResourceException("ability '$abilityEnum->value' not found in the system");
        }

        $user = MoonShineAuth::getGuard()->user();

        $checkCustomRules = moonshineConfig()
            ->getAuthorizationRules()
            ->every(fn ($rule) => $rule($this, $user, $abilityEnum->value, $this->getItem() ?? $this->getModel()));

        if (! $checkCustomRules) {
            return false;
        }

        if (! $this->isWithPolicy()) {
            return true;
        }

        return Gate::forUser($user)
            ->allows($abilityEnum->value, $this->getItem() ?? $this->getModel());
    }

    public function isWithPolicy(): bool
    {
        return $this->withPolicy;
    }
}
