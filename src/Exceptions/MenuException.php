<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Exceptions;

use Exception;

final class MenuException extends Exception
{
    public static function onlyMenuItemAllowed(): self
    {
        return new static("Only MenuSection allowed");
    }
}
