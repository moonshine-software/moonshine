<?php

declare(strict_types=1);

namespace MoonShine\UI\Exceptions;

use MoonShine\Core\Exceptions\MoonShineException;

class FieldException extends MoonShineException
{
    public static function resourceRequired(string $fieldClass, ?string $fieldIdentification = null): static
    {
        return new static(
            "Resource is required for $fieldClass"
            . ($fieldIdentification ? " ($fieldIdentification)" : "")
        );
    }

    public static function notFound(): static
    {
        return new static('Field not found');
    }
}
