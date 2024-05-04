<?php

declare(strict_types=1);

namespace MoonShine\Exceptions;

class ResourceException extends MoonShineException
{
    public static function required(): self
    {
        return new self("Resource is required");
    }

    public static function notDeclared(): self
    {
        return new self("Resource is not declared. Declare the resource in the MoonShineServiceProvider");
    }
}
