<?php

declare(strict_types=1);

namespace MoonShine\UI\Exceptions;

use MoonShine\Core\Exceptions\MoonShineException;

class ActionButtonException extends MoonShineException
{
    public static function resourceRequired(): static
    {
        return new static('Resource is required for action');
    }
}
