<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Exceptions;

use Exception;

final class MoonShineException extends Exception
{
    public static function onlyResourceAllowed(): self
    {
        return new static('Only Resource allowed');
    }
}
