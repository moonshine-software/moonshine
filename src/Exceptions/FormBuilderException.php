<?php

declare(strict_types=1);

namespace MoonShine\Exceptions;

use Exception;

class FormBuilderException extends Exception
{
    public static function resourceRequired(): self
    {
        return new self("Resource is required");
    }
}
