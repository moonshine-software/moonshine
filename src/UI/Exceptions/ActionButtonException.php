<?php

declare(strict_types=1);

namespace MoonShine\UI\Exceptions;

use MoonShine\Core\Exceptions\MoonShineException;

class ActionButtonException extends MoonShineException
{
    public static function resourceRequired(): self
    {
        return new self("Resource is required for action");
    }
}
