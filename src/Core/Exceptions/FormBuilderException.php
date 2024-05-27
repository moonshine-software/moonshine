<?php

declare(strict_types=1);

namespace MoonShine\Core\Exceptions;

class FormBuilderException extends MoonShineException
{
    public static function resourceRequired(): self
    {
        return new self("Resource is required");
    }
}
