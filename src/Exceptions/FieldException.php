<?php

declare(strict_types=1);

namespace MoonShine\Exceptions;

use Exception;

class FieldException extends Exception
{
    public static function resourceRequired(string $fieldClass, string $fieldIdentification): self
    {
        return new self("Resource is required for $fieldClass ($fieldIdentification)");
    }
}
