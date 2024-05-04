<?php

declare(strict_types=1);

namespace MoonShine\Exceptions;

class ActionException extends MoonShineException
{
    public static function resourceRequired(): self
    {
        return new self("Resource is required for action");
    }
}
