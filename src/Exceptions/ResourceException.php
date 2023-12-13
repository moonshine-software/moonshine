<?php

declare(strict_types=1);

namespace MoonShine\Exceptions;

use Exception;

class ResourceException extends Exception
{
    public static function notDeclared(): self
    {
        return new self("Resource is not declared. Declare the resource in the MoonShineServiceProvider");
    }
}
