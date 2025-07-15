<?php

declare(strict_types=1);

namespace MoonShine\Crud\Exceptions;

use MoonShine\Core\Exceptions\MoonShineException;
use MoonShine\Support\Enums\Ability;

final class CrudResourceException extends MoonShineException
{
    public static function resourceOrFieldRequired(): self
    {
        return new self('Resource or Field is required');
    }

    public static function abilityNotFound(string|Ability $ability): self
    {
        $value = \is_string($ability) ? $ability : $ability->value;

        return new self("ability '$value' not found in the system");
    }

    public static function relationNotFound(string $relationName): self
    {
        return new self("Relation $relationName not found for current resource");
    }
}
