<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Resource;

use Illuminate\Support\Facades\Gate;
use MoonShine\Core\Exceptions\ResourceException;
use MoonShine\Laravel\Enums\Ability;
use MoonShine\Laravel\MoonShineAuth;

trait ResourceWithAuthorization
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
        $abilityEnum = $ability instanceof Ability ? $ability : Ability::tryFrom($ability);

        if (is_null($abilityEnum) || ! in_array($abilityEnum, $this->getGateAbilities(), true)) {
            throw new ResourceException("ability '$abilityEnum->value' not found in the system");
        }

        return $this->isCan($ability);
    }

    protected function isCan(Ability $ability): bool
    {
        return true;
    }

    public function isWithPolicy(): bool
    {
        return $this->withPolicy;
    }
}
